<?php

class UFCOE_RoleProvider_Test1 extends UFCOE_RoleProvider {
    public function fetchRoles($username) {
        return array('user1-' . $username, 'time1-' . time());
    }
}