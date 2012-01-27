<?php

/*
 * Role Provider for a Drupal installation. It's tables must be in the same DB as
 * Elgg.
 */
class UFCOE_RoleProvider_Drupal extends UFCOE_RoleProvider {

    protected $prefix;

    /**
     * @param string $prefix table name prefix for Drupal
     */
    public function __construct($prefix = '') {
        $this->prefix = $prefix;
    }

    /**
     * Fetch Drupal roles for a given user
     *
     * @param string $username
     * @return array of role names for that user
     */
    public function fetchRoles($username) {
        return $this->fetchRolesByElggUsername($username);
    }


    /**
     * @return bool
     */
    public function hasShibAuthmap()
    {
        static $val = null;
        if (null === $val) {
            $data = get_data("SHOW TABLES LIKE '{$this->prefix}shib_authmap'");
            $val = (bool) $data;
        }
        return $val;
    }

    /**
     * @param string $username
     * @return int|null
     */
    public function getDrupalUidFromElggUsername($username)
    {
        $escapedUsername = mysql_real_escape_string($username);
        if ($this->hasShibAuthmap()) {
            $data = get_data("
                SELECT uid FROM {$this->prefix}shib_authmap WHERE targeted_id = '{$escapedUsername}'
            ");
            if (isset($data[0]->uid)) {
                return (int) $data[0]->uid;
            }
        }
        return null;
    }

    /**
     * @param string $username
     * @return array
     */
    public function fetchRolesByElggUsername($username)
    {
        $uid = $this->getDrupalUidFromElggUsername($username);
        return ($uid)
            ? $this->fetchRolesByDrupalUid($uid)
            : array();
    }

    /**
     * @param int $uid
     * @return array
     */
    public function fetchRolesByDrupalUid($uid)
    {
        $roles = array();
        $data = get_data("
            SELECT r.name
            FROM {$this->prefix}users_roles ur
            JOIN {$this->prefix}role r ON (ur.rid = r.rid)
            WHERE ur.uid = " . (int) $uid . "
            ORDER BY r.weight
        ");
        if ($data) {
            foreach ($data as $row) {
                $roles[$row->name] = true;
            }
        }
        return array_keys($roles);
    }

    /**
     * @return array
     */
    public function fetchAvailableRoles()
    {
        $roles = array();
        $data = get_data("SELECT r.name FROM {$this->prefix}role r ORDER BY r.weight");
        if ($data) {
            foreach ($data as $row) {
                $roles[$row->name] = true;
            }
        }
        return array_keys($roles);
    }

    /**
     * @param string $username
     * @return array
     */
    public function fetchRolesByDrupalUsername($username)
    {
        $escapedUsername = mysql_real_escape_string($username);
        $data = get_data("
            SELECT uid FROM {$this->prefix}users WHERE `name` = '{$escapedUsername}'
        ");
        return (isset($data[0]->uid))
            ? $this->fetchRolesByDrupalUid($data[0]->uid)
            : array();
    }
}
