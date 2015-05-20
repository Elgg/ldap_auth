<?php
/**
* Elgg LDAP authentication
*
* @package ElggLDAPAuth
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
* @author Misja Hoebe <misja.hoebe@gmail.com>
* @link http://community.elgg.org/pg/profile/misja
*/

$hostname_label = elgg_echo('ldap_auth:settings:label:hostname');
$hostname_descr = elgg_echo('ldap_auth:settings:help:hostname');
$hostname_input = elgg_view('input/text', array(
	'name' => "params[hostname]",
	'value' => $vars['entity']->hostname,
));

$port_label = elgg_echo('ldap_auth:settings:label:port');
$port_descr = elgg_echo('ldap_auth:settings:help:port');
$port_input = elgg_view('input/text', array(
	'name' => "params[port]",
	'value' => $vars['entity']->port,
));

$version_label = elgg_echo('ldap_auth:settings:label:version');
$version_descr = elgg_echo('ldap_auth:settings:help:version');
$version_input = elgg_view('input/dropdown', array(
	'name' => "params[version]",
	'value' => $vars['entity']->version,
	'options' => array(1, 2, 3)
));

$bind_dn_label = elgg_echo('ldap_auth:settings:label:bind_dn');
$bind_dn_descr = elgg_echo('ldap_auth:settings:help:bind_dn');
$bind_dn_input = elgg_view('input/text', array(
	'name' => "params[bind_dn]",
	'value' => $vars['entity']->bind_dn,
));

$bind_password_label = elgg_echo('ldap_auth:settings:label:bind_password');
$bind_password_descr = elgg_echo('ldap_auth:settings:help:bind_password');
$bind_password_input = elgg_view('input/password', array(
	'name' => "params[bind_password]",
	'value' => $vars['entity']->bind_password,
));

$basedn_label = elgg_echo('ldap_auth:settings:label:basedn');
$basedn_descr = elgg_echo('ldap_auth:settings:help:basedn');
$basedn_input = elgg_view('input/text', array(
	'name' => "params[basedn]",
	'value' => $vars['entity']->basedn,
));

$filter_attr_label = elgg_echo('ldap_auth:settings:label:filter_attr');
$filter_attr_descr = elgg_echo('ldap_auth:settings:help:filter_attr');
$filter_attr_input = elgg_view('input/text', array(
	'name' => "params[filter_attr]",
	'value' => $vars['entity']->filter_attr,
));

$search_attr_label = elgg_echo('ldap_auth:settings:label:search_attr');
$search_attr_descr = elgg_echo('ldap_auth:settings:help:search_attr');
$search_attr_input = elgg_view('input/text', array(
	'name' => "params[search_attr]",
	'value' => $vars['entity']->search_attr,
));

$group_dn_label = elgg_echo('ldap_auth:settings:label:group_dn');
$group_dn_descr = elgg_echo('ldap_auth:settings:help:group_dn');
$group_dn_input = elgg_view('input/text', array(
        'name' => "params[group_dn]",
        'value' => $vars['entity']->group_dn,
));

$group_attr_label = elgg_echo('ldap_auth:settings:label:group_attr');
$group_attr_descr = elgg_echo('ldap_auth:settings:help:group_attr');
$group_attr_input = elgg_view('input/text', array(
        'name' => "params[group_attr]",
        'value' => $vars['entity']->group_attr,
));

$user_create_label = elgg_echo('ldap_auth:settings:label:user_create');
$user_create_descr = elgg_echo('ldap_auth:settings:help:user_create');
$user_create_input = elgg_view('input/dropdown', array(
	'name' => "params[user_create]",
	'value' => $vars['entity']->user_create,
	'options' => array('on', 'off'),
));

$legend1 = elgg_echo('ldap_auth:settings:label:host');
$legend2 = elgg_echo('ldap_auth:settings:label:connection_search');;

echo <<<FORM
<div>
	<fieldset style="border: 1px solid; padding: 15px; margin: 10px">
		<legend>$legend1</legend>
		<div>
			<label>$hostname_label</label>
			$hostname_input
			$hostname_descr
		</div>

		<div>
			<label>$port_label</label>
			$port_input
			$port_descr
		</div>

		<div>
			<label>$version_label</label>
			$version_input<br />
			$version_descr
		</div>
	</fieldset>

	<fieldset style="border: 1px solid; padding: 15px; margin: 0 10px 0 10px">
		<legend>$legend2</legend>
		<div>
			<label>$bind_dn_label</label>
			$bind_dn_input
			$bind_dn_descr
		</div>

		<div>
			<label>$bind_password_label</label><br />
			$bind_password_input<br />
			$bind_password_descr
		</div>

		<div>
			<label>$basedn_label</label>
			$basedn_input
			$basedn_descr
		</div>

		<div>
			<label>$filter_attr_label</label>
			$filter_attr_input<br />
			$filter_attr_descr
		</div>

		<div>
			<label>$search_attr_label</label>
			$search_attr_input<br />
			$search_attr_descr
		</div>

		<div>
			<label>$group_dn_label</label>
            $group_dn_input
            $group_dn_descr
		</div>

		<div>
			<label>$group_attr_label</label>
			$group_attr_input
			$group_attr_descr
		</div>
		
		<div>
			<label>$user_create_label</label>
			$user_create_input<br />
			$user_create_descr
		</div>
	</fieldset>
</div>
FORM;
