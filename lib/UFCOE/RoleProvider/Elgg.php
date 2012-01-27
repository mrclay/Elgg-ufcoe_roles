<?php

/**
 * Role provider for Elgg. Easy! Admin or not...
 */
class UFCOE_RoleProvider_Elgg extends UFCOE_RoleProvider {

    /**
     * Get roles from backend (the return of this function will be cached for cacheTtl seconds)
     *
     * @param string $username
     * @return array of role names
     */
    public function fetchRoles($username) {
        $user = get_user_by_username($username);
        $roles = array();
        if ($user && $user->isAdmin()) {
            $roles[] = 'admin';
        }
        return $roles;
    }
}