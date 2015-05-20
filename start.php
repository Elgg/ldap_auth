<?php
/**
 * Elgg LDAP authentication
 *
 * @package ElggLDAPAuth
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Misja Hoebe <misja.hoebe@gmail.com>
 * @link http://community.elgg.org/profile/misja
 */

// Register the initialization function
elgg_register_event_handler('init', 'system', 'ldap_auth_init');

/**
 * LDAP Authentication init
 */
function ldap_auth_init() {
	// Register the authentication handler
	register_pam_handler('ldap_auth_authenticate');
}

/**
 * Get an instance of the LdapServer class
 *
 * @return
 */
function ldap_auth_get_server() {
	$settings = elgg_get_plugin_from_id('ldap_auth');

	static $server;

	if (!$server) {
		try {
			$server = new LdapServer($settings);
		} catch (Exception $e) {
			elgg_log($e->getMessage());

			return false;
		}
	}

	return $server;
}

/**
 * Authenticate user against the credential
 *
 * @param array $credentials
 * @return boolean
 */
function ldap_auth_authenticate($credentials) {
	$settings = elgg_get_plugin_from_id('ldap_auth');

	$server = ldap_auth_get_server();

	if (!$server) {
		// Unable to connect to LDAP server
		register_error(elgg_echo('ldap_auth:connection_error'));
		return false;
	}

	$settings = elgg_get_plugin_from_id('ldap_auth');

	$username = elgg_extract('username', $credentials);
	$password = elgg_extract('password', $credentials);

	$filter = "({$settings->filter_attr}={$username})";

	if (!$server->bind()) {
		register_error(elgg_echo('ldap_auth:connection_error'));
		return false;
	}

	$result = $server->search($filter);

	if (empty($result)) {
		// User was not found
		return false;
	}

	// Bind using user's distinguished name and password
	$success = $server->bind($result['dn'], $password);

	if (!$success) {
		// dn/password combination doesn't exist
		return false;
	}

	// Check if the user is a member of the group, in case a groupOfNames is included in settings
    $result2 = $server->isMember($result['dn']);
    if (!$result2) {
        //elgg_log("User found in directory and its bind completed ok, but is not a member of the required group","NOTICE");
        register_error(elgg_echo('ldap_auth:not_in_group'));
        return false;
    }
	
	$user = get_user_by_username($username);

	if ($user) {
		return login($user);
	}

	if ($settings->create_user !== 'off') {
		return ldap_auth_create_user($username, $password, $result);
	}

	register_error(elgg_echo("ldap_auth:no_account"));
	return false;
}

/**
 * Create a new user from the data provided by LDAP
 *
 * @param string $username
 * @param string $password
 * @param array  $data Data fetched from LDAP
 */
function ldap_auth_create_user($username, $password, $data) {
	// Check that we have the values. register_user() will take
	// care of more detailed validation.
	$firstname = elgg_extract('firstname', $data);
	$lastname  = elgg_extract('lastname', $data);
	$email     = elgg_extract('mail', $data);

	// Combine firstname and lastname
	$name = implode(' ', array($firstname, $lastname));

	try {
		$guid = register_user($username, $password, $name, $email);
	} catch (Exception $e) {
		register_error($e->getMessage());
		return false;
	}

	if (!$guid) {
		register_error(elgg_echo('ldap_auth:no_register'));
		elgg_log("Failed to create an account for LDAP user $username");
		return false;
	}

	$user = get_entity($guid);

	// Allow plugins to respond to the registration
	$params = array(
		'user' => $user,
		'ldap_entry' => $data,
	);

	if (!elgg_trigger_plugin_hook('register', 'user', $params, true)) {
		// For some reason one of the plugins returned false.
		// This most likely means that something went wrong
		// and we will have to remove the user.
		$user->delete();

		register_error(elgg_echo('registerbad'));

		return false;
	}

	// Validate the user
	elgg_set_user_validation_status($guid, true, 'LDAP plugin based validation');

	return true;
}
