<?php

elgg_register_event_handler('init', 'system', 'ufcoe_roles_init');

function ufcoe_roles_init() {
    require_once __DIR__ . '/lib/UFCOE/RoleProvider.php';
    require_once __DIR__ . '/lib/UFCOE/RoleProvider/Elgg.php';
    require_once __DIR__ . '/lib/UFCOE/RoleReader.php';

    // remove cached roles on login/logout
    elgg_register_event_handler('login' ,'user','_ufcoe_roles_handle_loginout');
    elgg_register_event_handler('logout','user','_ufcoe_roles_handle_loginout');

    elgg_register_event_handler('pagesetup', 'system', 'ufcoe_roles_settings_sidebar');
}

function _ufcoe_roles_handle_loginout($event, $type, $user) {
    // no need to instantiate reader
    unset($_SESSION['ufcoe_roles']);
    return true;
}

/**
 * @param string $role_name
 * @param string $provider_key
 * @return bool
 */
function ufcoe_roles_logged_in_user_has($role_name, $provider_key = '') {
    $roles = ufcoe_roles_get_roles_array();
    if ($provider_key) {
        return isset($roles[$provider_key]) && in_array($role_name, $roles[$provider_key]);
    }
    return in_array($role_name, $roles[UFCOE_RoleReader::KEY_ALL]);
}

/**
 * Get user's roles as an array (using $_SESSION as cache for current user)
 * @param string $username
 * @return array
 */
function ufcoe_roles_get_roles_array($username = null) {
    if (! $username) {
        $logged_in_user = elgg_get_logged_in_user_entity();
        if ($logged_in_user) {
            $username = $logged_in_user->get('username');
        }
    }
    return empty($username)
        ? array()
        : ufcoe_roles_get_reader_instance()->getRoles($username);
}

/**
 * Get user's roles as a string (using $_SESSION as cache for current user)
 * @param string $username
 * @return string
 */
function ufcoe_roles_get_roles_string($username = null) {
    return ufcoe_roles_stringize(ufcoe_roles_get_roles_array($username));
}

/**
 * @param string $key
 * @param UFCOE_RoleProvider $provider
 */
function ufcoe_roles_add_provider($key, UFCOE_RoleProvider $provider) {
    ufcoe_roles_get_reader_instance()->addProvider($key, $provider);
}

/**
 * @param array $roles
 * @return string
 */
function ufcoe_roles_stringize(array $roles) {
    $ret = array();
    foreach ($roles as $key => $roleSet) {
        if ($key === UFCOE_RoleReader::KEY_ALL) {
            $ret += $roleSet;
        } else {
            foreach ($roleSet as $role) {
                $ret[] = "$key:$role";
            }
        }
    }
    return implode(';', $ret);
}

/**
 * @return UFCOE_RoleReader
 */
function ufcoe_roles_get_reader_instance() {
    static $instance = null;
    if (null === $instance) {
        $instance = new UFCOE_RoleReader();
        $instance->addProvider('elgg', new UFCOE_RoleProvider_Elgg());
    }
    return $instance;
}

/**
 * @param string $key of provider
 * @param string $alias of provider system
 * @param string $lang
 */
function ufcoe_roles_add_key_alias($key, $alias, $lang = "en") {
    $en["ufcoe_roles:key:" . $key] = $alias;
    add_translation("en", $en);
}


/**
 * Alter settings sidebar menu
 */
function ufcoe_roles_settings_sidebar() {
    if (elgg_get_context() == "settings") {
        $roles = ufcoe_roles_get_roles_array();
        if (! empty($roles[UFCOE_RoleReader::KEY_ALL])) {
            $params = array(
                'name' => 'ufcoe_roles_my',
                'text' => elgg_echo('ufcoe_roles:settings:menu:my'),
                'href' => "mod/ufcoe_roles/my.php",
            );
            elgg_register_menu_item('page', $params);
        }
    }
}