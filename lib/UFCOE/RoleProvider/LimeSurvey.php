<?php

/*
 * Role Provider class for LimeSurvey
 */
class UFCOE_RoleProvider_LimeSurvey extends UFCOE_RoleProvider {

    private $pdo;
    private $prefix;

    /**
     * @param PDO $pdo_handle if null is given, the Limesurvey tables must be in
     *                        the Elgg DB.
     * @param string $db_prefix prefix on Limesurvey table names
     */
    public function __construct(PDO $pdo_handle = null, $db_prefix = '') {
        $this->pdo = $pdo_handle;
        $this->prefix = $db_prefix;
    }

    /**
     * @param string $username
     * @return array
     */
    public function fetchRoles($username) {
        $roles = array();
        $role_cols = array('create_survey', 'create_user', 'delete_user', 'superadmin',
                           'configurator','manage_template', 'manage_label');

        $quotedUsername = $this->pdo
            ? $this->pdo->quote($username)
            : "'" . mysql_real_escape_string($username) . "'";

        $sql = sprintf("
            SELECT " . implode(',', $role_cols) . "
            FROM {$this->prefix}users
            WHERE users_name = %s",
            $quotedUsername
        );
        if ($this->pdo) {
            $row = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                foreach ($role_cols as $key) {
                    if ($row[$key]) {
                        $roles[] = $key;
                    }
                }
            }
        } else {
            $row = get_data_row($sql);
            if ($row) {
                foreach ($role_cols as $key) {
                    if ($row->{$key}) {
                        $roles[] = $key;
                    }
                }
            }
        }
        return $roles;
    }
}
