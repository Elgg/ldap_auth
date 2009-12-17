<?php
    /**
     * Elgg LDAP authentication
     *
     * @package ElggLDAPAuth
     * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
     * @author Misja Hoebe <misja.hoebe@gmail.com>
     * @link http://community.elgg.org/pg/profile/misja
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
        global $CONFIG;

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
        $config = find_plugin_settings('ldap_auth');

        // Nothing to do if not configured
        if (!$config)
        {
            return false;
        }

        $username      = null;
        $password      = null;

        if (is_array($credentials) && ($credentials['username']) && ($credentials['password']))
        {
            $username = utf8_encode($credentials['username']);
            $password = utf8_encode(html_entity_decode($credentials['password']));
        }
        else
        {
            return false;
        }

        // Perform the authentication
        return ldap_auth_check($config, $username, $password);
    }

    /**
     * Perform an LDAP authentication check
     *
     * @param ElggPlugin $config
     * @param string $username
     * @param string $password
     * @return boolean
     */
    function ldap_auth_check($config, $username, $password)
    {
        $host = $config->hostname;

        // No point continuing
        if(empty($host))
        {
            error_log("LDAP error: no host configured.");
            return;
        }

        $port        = $config->port;
        $version     = $config->version;
        $basedn      = $config->basedn;
        $filter_attr = $config->filter_attr;
        $search_attr = $config->search_attr;
        $bind_dn     = utf8_encode($config->ldap_bind_dn);
        $bind_pwd    = utf8_encode($config->ldap_bind_pwd);
        $user_create = $config->user_create;

        ($user_create == 'on') ? $user_create = true : $user_create = false;

        $port        ? $port        : $port = 389;
        $version     ? $version     : $version = 3;
        $filter_attr ? $filter_attr : $filter_attr = 'uid';
        $basedn      ? $basedn = array_map('trim', explode(':', $basedn)) : $basedn = array();

        if (!empty($search_attr))
        {
            // $search_attr as in "email:email_address, name:name_name";

            $pairs = array_map('trim',explode(',', $search_attr));

            $values = array();

            foreach ($pairs as $pair)
            {
                $parts = array_map('trim', explode(':', $pair));

                $values[$parts[0]] = strtolower($parts[1]);
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

                    if ($user = get_user_by_username($username))
                    {
                        // User exists, login
                        return login($user);
                    }
                    else
                    {
                        // Valid login but user doesn't exist

                        if ($user_create)
                        {
                            $name  = $ldap_user_info['firstname'];

                            if (isset($ldap_user_info['lastname']))
                            {
                                $name  = $name . " " . $ldap_user_info['lastname'];
                            }

                            ($ldap_user_info['mail']) ? $email = $ldap_user_info['mail'] : $email = null;

                            if ($guid = register_user($username, $password, $name, $email))
                            {
                                // Registration successful, validate the user
                                set_user_validation_status($guid, true, 'LDAP plugin based validation');

                                // Success, credentials valid and account has been created
                                return true;
                            }
                            else
                            {
                                register_error(elgg_echo('ldap_auth:no_register'));

                                return false;
                            }
                        }
                        else
                        {
                            register_error(elgg_echo("ldap_auth:no_account"));

                            return false;
                        }
                    }
                }
                else
                {
                    ldap_close($ds);

                    return false;
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

    /**
     * Create an LDAP connection
     *
     * @param string $host
     * @param int $port
     * @param int $version
     * @param string $bind_dn
     * @param string $bind_pwd
     * @return mixed LDAP link identifier on success, or false on error
     */
    function ldap_auth_connect($host, $port, $version, $bind_dn, $bind_pwd)
    {
        $ds = @ldap_connect($host, $port);

        @ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, $version);

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
            error_log('Unable to bind to the LDAP server with provided credentials: '.ldap_error($ds));

            ldap_close($ds);

            return false;
        }

        return $ds;
    }

    /**
     * Performs actual LDAP authentication
     *
     * @param object $ds LDAP link identifier
     * @param string $basedn
     * @param string $username
     * @param string $password
     * @param string $filter_attr
     * @param string $search_attr
     * @return mixed array with search attributes or false on error
     */
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

        if (@ldap_bind($ds, utf8_encode($entry[0]['dn']), $password))
        {
            // We have a bind, a valid login

            foreach (array_keys($search_attr) as $attr)
            {
                $ldap_user_info[$attr] = $entry[0][$search_attr[$attr]][0];
            }

            return $ldap_user_info;
        }

        return false;
    }
?>
