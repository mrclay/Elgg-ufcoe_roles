<?php

class UFCOE_RoleProvider_Test2 extends UFCOE_RoleProvider {
    public function fetchRoles($username) {
        return array('user2-' . $username, 'time2-' . time());
    }
}