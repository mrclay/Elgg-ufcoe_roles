<?php
die('Disabled');

require __DIR__ . '/../../../engine/start.php';
ini_set('display_errors', 1);

require __DIR__ . '/../lib/UFCOE/RoleProvider/Test1.php';
require __DIR__ . '/../lib/UFCOE/RoleProvider/Test2.php';

$test1 = new UFCOE_RoleProvider();
$test1->setCacheTtl(20);
ufcoe_roles_set_provider('t1', $test1);

$test2 = new UFCOE_RoleProvider();
$test2->setCacheTtl(5);
ufcoe_roles_set_provider('t2', $test2);

header('Content-Type: text/plain');

var_export(ufcoe_roles_get_roles_array()); echo "\n\n";

var_export(ufcoe_roles_get_roles_string());
