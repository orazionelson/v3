<?php
require "vcube.class.php";
//Verify
$gump = new GUMP(); 
$_GET = $gump->sanitize($_GET); // You don't have to sanitize, but it's safest to do so.

$ajax_validator = new Vcube();

$rules=$ajax_validator->get_validation_rules_array();
$field_name=array_keys($_GET);
//var_dump($rules);
$subrules[$field_name[0]]=$rules[$field_name[0]];
$is_valid = Vcube::is_valid($_GET, $subrules);

$ajax_validator->get_response_code($is_valid);

?>
