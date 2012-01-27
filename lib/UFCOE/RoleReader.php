<?php

class UFCOE_RoleReader {

    const KEY_ALL = '__GLOBAL__';

    protected $providers = array();

    /**
     * @var array cache for the duration of the request
     */
    protected $requestCache = array();

    /**
     * @var bool did someone read the roles?
     */
    protected $rolesWereRead = false;

    /**
     * @param string $key
     * @param UFCOE_RoleProvider $provider
     * @return UFCOE_RoleReader
     */
    public function addProvider($key, UFCOE_RoleProvider $provider)
    {
        $this->providers[$key] = $provider;
        if ($this->rolesWereRead) {
            // the caches might be incomplete
            $this->emptyCaches();
        }
        return $this;
    }

    /**
     * @param string $key
     * @return UFCOE_RoleProvider|null
     */
    public function getProvider($key)
    {
        return isset($this->providers[$key]) ? $this->providers[$key] : null;
    }

    /**
     * Get roles for user. If the username is the current Elgg user, manage these in
     * the session for performance
     *
     * @param string $username
     * @return array
     */
    public function getRoles($username)
    {
        if (empty($username)) {
            return array();
        }
        if (! array_key_exists($username, $this->requestCache)) {
            $loggedInUser = elgg_get_logged_in_user_entity();
            $isLoggedInUser = ($loggedInUser && $loggedInUser->get('username') === $username);
            $roles = array();
            $roles[self::KEY_ALL] = array();
            foreach ($this->providers as $key => $provider) {
                /* @var UFCOE_RoleProvider $provider */
                if ($isLoggedInUser) {
                    $rolesToAdd = $this->getRolesFromSession($key, $provider, $username);
                } else {
                    $rolesToAdd = $provider->fetchRoles($username);
                }
                $roles[$key] = $rolesToAdd;
                $roles[self::KEY_ALL] = array_merge($roles[self::KEY_ALL], $rolesToAdd);
            }
            $this->rolesWereRead = true;
            $this->requestCache[$username] = $roles;
        }
        return $this->requestCache[$username];
    }

    /**
     * @param string $key
     * @param UFCOE_RoleProvider $provider
     * @param string $username
     * @return array
     */
    protected function getRolesFromSession($key, UFCOE_RoleProvider $provider, $username)
    {
        if (! isset($_SESSION['ufcoe_roles'])) {
            $_SESSION['ufcoe_roles'] = array();
        }
        if (! empty($_SESSION['ufcoe_roles'][$key])) {
            list($time, $roles) = explode('|', $_SESSION['ufcoe_roles'][$key]);
            if (time() - $time < $provider->getCacheTtl()) {
                return ($roles === '')
                    ? array()
                    : explode(';', $roles);
            }
        }
        // not in session/or session is stale
        $roles = $provider->fetchRoles($username);
        $_SESSION['ufcoe_roles'][$key] = time() . "|" . implode(';', $roles);
        return $roles;
    }

    /**
     * @return UFCOE_RoleReader
     */
    public function emptyCaches()
    {
        $_SESSION['ufcoe_roles'] = array();
        $this->requestCache = array();
        return $this;
    }
}