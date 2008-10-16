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

    $russian = array(
        'ldap_auth:settings:label:host' => "Хост",
        'ldap_auth:settings:label:connection_search' => "Настройки LDAP",
        'ldap_auth:settings:label:hostname' => "Имя хоста",
        'ldap_auth:settings:help:hostname' => "Введите каноническое имя хоста, например <i>ldap.yourcompany.com</i>",
        'ldap_auth:settings:label:port' => "Порт LDAP сервера",
    	'ldap_auth:settings:help:port' => "Порт LDAP сервера, по умолчанию 389, большинство серверов используют именно его.",
        'ldap_auth:settings:label:version' => "Версия LDAP протокола",
        'ldap_auth:settings:help:version' => "Версия LDAP протокола. По умолчанию 3, большинство серверов используют именно его.",
        'ldap_auth:settings:label:ldap_bind_dn' => "DN для соединения с LDAP",
        'ldap_auth:settings:help:ldap_bind_dn' => "Какой использовать DN для не-анонимного соединения, например <i>cn=admin,dc=yourcompany,dc=com</i>",
        'ldap_auth:settings:label:ldap_bind_pwd' => "Пароль для соединения с LDAP",
        'ldap_auth:settings:help:ldap_bind_pwd' => "Какой использовать пароль для не-анонимного соединения.",
        'ldap_auth:settings:label:basedn' => "Базовый DN",
        'ldap_auth:settings:help:basedn' => "Базовый DN. Используйте двоеточие (:) что бы ввести несколько DN, например <i>dc=yourcompany,dc=com : dc=othercompany,dc=com</i>",
        'ldap_auth:settings:label:filter_attr' => "В каком аттрибуте содержится Username",
        'ldap_auth:settings:help:filter_attr' => "В каком аттрибуте содержится username, обычно это <i>cn</i>, <i>uid</i> или <i>sAMAccountName</i>.",
        'ldap_auth:settings:label:search_attr' => "Аттрибуты для поиска",
        'ldap_auth:settings:help:search_attr' => "Введите аттрибуты для поиска как пары ключ:значение, где ключ является аттрибутом в Elgg, а значение - его эквивалентом в LDAP .
         Ключи <i>firstname</i>, <i>lastname</i> and <i>mail</i> используются для создания пользователя в Elgg. Следующий пример будет работать для ActiveDirectory:<br/>
         <blockquote><i>firstname:givenname, lastname:sn, mail:mail</i></blockquote>",
        'ldap_auth:settings:label:user_create' => "Создать пользователя",
        'ldap_auth:settings:help:user_create' => "Создавать пользователя в Elgg если удалось его авторизовать в LDAP.",
        'ldap_auth:no_account' => "Ваши имя пользователя и пароль корректны, но ваш аккаунт не найден - пожалуйста, свяжитесь с администрацией",
        'ldap_auth:no_register' => 'Не удалось создать для вас аккаунт - пожалуйста, свяжитесь с администрацией.'
    );
    
    add_translation('ru', $russian);
?>
