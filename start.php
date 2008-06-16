<?php
    /**
	 * Elgg LDAP authentication
	 * 
	 * @package ElggLDAPAuth
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Misja Hoebe <misja@elgg.com>
	 * @copyright Curverider Ltd 2008
	 * @link http://elgg.com
	 */

	/**
	 * LDAP Authentication init
	 * 
	 * These parameters are required for the event API, but we won't use them:
 	 * 
	 * @param unknown_type $event
	 * @param unknown_type $object_type
	 * @param unknown_type $object 
	 */
	function ldap_auth_init()
	{
	    // Register the authentication handler
	    register_pam_handler('ldap_auth_authenticate');	    
	}
	
	// Register the initialisation function
	register_elgg_event_handler('init','system','ldap_auth_init');
	    
	/**
	 * LDAP authentication
	 * 
	 * @param mixed $credentials PAM handler specific credentials
	 * @return boolean
	 */
	function ldap_auth_authenticate($credentials = null)
    {
        // Nothing to do if LDAP module not installed
        if (!function_exists('ldap_connect')) return false;
        
        // Get configuration settings
        $config = get_config('ldap_auth:settings');

        // Nothing to do if not configured
        if (!$config) return false;
        
        $username      = null;
        $password      = null;
        $authenticated = false;
        
        if (is_array($credentials) && ($credentials['username']) && ($credentials['password']))
        {
            $username = $credentials['username'];
            $password = $credentials['password'];
        }
        else
        {
            return false;
        }
        
        // Perform the authentication
        foreach ($config as $provider)
        {
            if (auth_ldap_check($provider, $username, $password)) break;
        }
    }
    
    function ldap_auth_check($provider, $username, $password)
    {
        $host        = $provider['host'];
        
        // No point continueing
        if(empty($host))
        {
            error_log("LDAP: no host configured.");
            return;
        }
        
        $port        = $provider['port'];
        $version     = $provider['version'];
        $ds          = $provider['ds'];
        $basedn      = $provider['basedn'];
        $filter_attr = $provider['filter_attr'];
        $search_attr = $provider['search_attr'];
        $bind_dn     = $provider['bind_dn'];
        $bind_pwd    = $provider['bind_pwd'];

        $port        ? $port                      : $port = 389;
        $version     ? $version                   : $version = 3;
        $basedn      ? $basedn = explode($basedn) : $basedn = array();
        $filter_attr ? $filter_attr               : $filter_attr = 'uid';
        
        if (!empty($search_attr))
        {
            // $search_attr as in "email:email_address, name:name_name";
        
            $pairs = array_map('trim',explode(',', $search_attr));
            
            $values = array();
            
            foreach ($pairs as $pair)
            {
                $parts = explode(':', $pair);
        
                $values[$parts[0]] = $parts[1];
            }
        
            $search_attr = $values;            
        }
        else
        {
            $search_attr = array('dn' => 'dn');
        }
        
        // Create a connection
        if ($ds = ldap_auth_connect($host, $port, $version, $bind_dn, $bind_pwd))
        {
            // Perform a search
            foreach ($basedn as $this_ldap_basedn)
            {
            	$ldap_user_info = ldap_auth_do_auth($ds, $this_ldap_basedn, $username, $password, $filter_attr, $search_attr);
    
            	if($ldap_user_info)
            	{
            		// LDAP login successful
    
                	// If a user should get created, do it here
    
            	    // Close the connection
                	ldap_close($ds);
    
    	            return true;
    			}
            }
    
            // Close the connection
            ldap_close($ds);
            
            return false;
        }
        else
        {
            return false;
        }
    }
    
    function ldap_auth_connect($host, $port, $version, $bind_dn, $bind_pwd)
    {
        $ds = @ldap_connect($host, $port);

        @ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, $protocol_version);

        // Start the LDAP bind process

        $ldapbind = null;

        if ($ds)
        {
            if ($bind_dn != '')
            {
                $ldapbind = @ldap_bind($ds, $bind_dn, $bind_pwd);
            }
            else
            {
                // Anonymous bind
                $ldapbind = @ldap_bind($ds);
            }
        }
        else
        {
            // Unable to connect
            error_log('Unable to connect to the LDAP server: '.ldap_error($ds));
            
            return false;
        }

        if (!$ldapbind)
        {
            error_log('Unable to bind to the LDAP server provided credentials: '.ldap_error($ds));
            
            ldap_close($ds);
            
            return false;
        }

        return $ds; 
    }
    
    function ldap_auth_do_auth($ds, $basedn, $username, $password, $filter_attr, $search_attr)
    {
        $sr = @ldap_search($ds, $basedn, $filter_attr ."=". $username, array_values($search_attr));

        if(!$sr)
        {
	        error_log('Unable to perform LDAP search: '.ldap_error($ds));

	        return false;
		}

        $entry = ldap_get_entries($ds, $sr);

        if(!$entry or !$entry[0])
        {
        	return false; // didn't find username
		}

        // Username exists, perform a bind for testing credentials

        if (@ldap_bind($ds, $entry[0]['dn'], $password) ) {

            // We have a bind, valid login

            foreach (array_keys($search_attr) as $attr)
            {
	        	$ldap_user_info[$attr] = $entry[0][$search_attr[$attr]][0];
			}

            return $ldap_user_info;
		}

        return false;
    }
?>