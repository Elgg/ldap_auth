<?
    /**
	 * Elgg LDAP authentication
	 * 
	 * @package ElggLDAPAuth
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Misja Hoebe <misja@elgg.com>
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.com
	 */
?>
<p>
    <fieldset style="border: 1px solid; padding: 15px; margin: 0 10px 0 10px">
        <legend><?php echo elgg_echo('ldap_auth:settings:label:host');?></legend>
        
        <label for="params[hostname]"><?php echo elgg_echo('ldap_auth:settings:label:hostname');?></label><br/>
        <div class="example"><?php echo elgg_echo('ldap_auth:settings:help:hostname');?></div>
        <input type="text" name="params[hostname]" value="<?php echo $vars['entity']->hostname;?>"/><br/>
        
        <label for="params[port]"><?php echo elgg_echo('ldap_auth:settings:label:port');?></label><br/>
        <div class="example"><?php echo elgg_echo('ldap_auth:settings:help:port');?></div>
        <input type="text" name="params[port]" value="<?php if (empty($vars['entity']->port)) {echo "389";} else {echo $vars['entity']->port;}?>"/><br/>

        <label for="params[version]"><?php echo elgg_echo('ldap_auth:settings:label:version');?></label><br/>
        <div class="example"><?php echo elgg_echo('ldap_auth:settings:help:version');?></div>
        <select name="params[version]">
            <option value="1" <?php if ($vars['entity']->version == 1) echo " selected=\"selected\" "; ?>>1</option>
            <option value="2" <?php if ($vars['entity']->version == 2) echo " selected=\"selected\" "; ?>>2</option>
            <option value="3" <?php if ((!$vars['entity']->version) || ($vars['entity']->version == 3)) echo " selected=\"selected\" "; ?>>3</option>
        </select>
    </fieldset>
</p>
<p>
    <fieldset style="border: 1px solid; padding: 15px; margin: 0 10px 0 10px">
        <legend><?php echo elgg_echo('ldap_auth:settings:label:connection_search');?></legend>

        <label for="params[ldap_bind_dn]"><?php echo elgg_echo('ldap_auth:settings:label:ldap_bind_dn');?></label><br/>
        <div class="example"><?php echo elgg_echo('ldap_auth:settings:help:ldap_bind_dn');?></div>
        <input type="text" size="50" name="params[ldap_bind_dn]" value="<?php echo $vars['entity']->ldap_bind_dn;?>"/><br/>

        <label for="params[ldap_bind_pwd]"><?php echo elgg_echo('ldap_auth:settings:label:ldap_bind_pwd');?></label><br/>
        <div class="example"><?php echo elgg_echo('ldap_auth:settings:help:ldap_bind_pwd');?></div>
        <input type="password" name="params[ldap_bind_pwd]" value="<?php echo $vars['entity']->ldap_bind_pwd;?>"/><br/>

        <label for="params[basedn]"><?php echo elgg_echo('ldap_auth:settings:label:basedn');?></label><br/>
        <div class="example"><?php echo elgg_echo('ldap_auth:settings:help:basedn');?></div>
        <input type="text" size="50" name="params[basedn]" value="<?php echo $vars['entity']->basedn;?>"/><br/>

        <label for="params[filter_attr]"><?php echo elgg_echo('ldap_auth:settings:label:filter_attr');?></label><br/>
        <div class="example"><?php echo elgg_echo('ldap_auth:settings:help:filter_attr');?></div>
        <input type="text" size="50" name="params[filter_attr]" value="<?php echo $vars['entity']->filter_attr;?>"/><br/>

        <label for="params[search_attr]"><?php echo elgg_echo('ldap_auth:settings:label:search_attr');?></label><br/>
        <div class="example"><?php echo elgg_echo('ldap_auth:settings:help:search_attr');?></div>
        <input type="text" size="50" name="params[search_attr]" value="<?php echo $vars['entity']->search_attr;?>"/><br/>

        <label for="params[user_create]"><?php echo elgg_echo('ldap_auth:settings:label:user_create');?></label><br/>
        <div class="example"><?php echo elgg_echo('ldap_auth:settings:help:user_create');?></div>
        <select name="params[user_create]">
            <option value="on" <?php if ($vars['entity']->user_create == 'on') echo " selected=\"selected\" "; ?>>Enabled</option>
            <option value="off" <?php if ($vars['entity']->user_create == 'off') echo " selected=\"selected\" "; ?>>Disabled</option>
        </select>
    </fieldset>
</p>