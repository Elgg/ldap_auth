<?php

    /**
	 * Elgg LDAP authentication
	 * 
	 * @package ElggLDAPAuth
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Misja Hoebe <misja@elgg.com>
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.com
	 */

    $en = array(
        'ldap_auth:settings:label:host' => "Host settings",
        'ldap_auth:settings:label:connection_search' => "LDAP settings",
        'ldap_auth:settings:label:hostname' => "Hostname",
        'ldap_auth:settings:help:hostname' => "Enter the canonical hostname, for example <i>ldap.yourcompany.com</i>",
        'ldap_auth:settings:label:port' => "The LDAP server port",
    	'ldap_auth:settings:help:port' => "The LDAP server port. Defaults is 389, which mosts hosts will use.",
        'ldap_auth:settings:label:version' => "LDAP protocol version",
        'ldap_auth:settings:help:version' => "LDAP protocol version. Defaults to 3, which most current LDAP hosts will use.",
        'ldap_auth:settings:label:ldap_bind_dn' => "LDAP bind DN",
        'ldap_auth:settings:help:ldap_bind_dn' => "Which DN to use for a non-anonymous bind, for exampe <i>cn=admin,dc=yourcompany,dc=com</i>",
        'ldap_auth:settings:label:ldap_bind_pwd' => "LDAP bind password",
        'ldap_auth:settings:help:ldap_bind_pwd' => "Which password to use when performing a non-anonymous bind.",
        'ldap_auth:settings:label:basedn' => "Based DN",
        'ldap_auth:settings:help:basedn' => "The base DN. Separate with a colon (:) to enter multiple DNs, for example <i>dc=yourcompany,dc=com : dc=othercompany,dc=com</i>",
        'ldap_auth:settings:label:filter_attr' => "Username filter attribute",
        'ldap_auth:settings:help:filter_attr' => "The filter to use for the username, common are <i>cn</i>, <i>uid</i> or <i>sAMAccountName</i>.",
        'ldap_auth:settings:label:search_attr' => "Search attributes",
        'ldap_auth:settings:help:search_attr' => "Enter search attibutes as key, value pairs with the key being the attribute description, and the value being the actual LDAP attribute.
         <i>firstname</i>, <i>lastname</i> and <i>mail</i> are used to create the Elgg user profile. The following example will work for ActiveDirectory:<br/>
         <blockquote><i>firstname:givenname, lastname:sn, mail:mail</i></blockquote>",
        'ldap_auth:settings:label:user_create' => "Create users",
        'ldap_auth:settings:help:user_create' => "Optionally, an account can get created when a LDAP authentication was succesful.",
        'ldap_auth:no_account' => "Your credentials are valid, but no account was found - please contact the system administrator",
        'ldap_auth:no_register' => 'An account could not get created for you - please contact the system administrator.'
    );
    
    add_translation('en', $en);
?>
