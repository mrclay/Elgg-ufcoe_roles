<?php

/**
 * Base class for a role provider. Extend this class and at least override fetchRoles().
 */
class UFCOE_RoleProvider {

    /**
     * @var int
     */
    protected $cacheTtl = 900;

    /**
     * Get roles from backend (the return of this function will be cached for cacheTtl seconds)
     *
     * @param string $username
     * @return array of role names
     */
    public function fetchRoles($username) {
        return array();
    }

    /**
     * @return int
     */
    public function getCacheTtl() {
        return $this->cacheTtl;
    }

    /**
     * @param int $ttl
     */
    public function setCacheTtl($ttl = 900) {
        $this->cacheTtl = (int) $ttl;
    }
}