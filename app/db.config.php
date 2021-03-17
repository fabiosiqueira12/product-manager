<?php

//DATABASE CONFIG
$DB_LOCAL = [
    'host' => 'localhost',
    'dbname' => 'product_manager',
    'user' => 'root',
    'pass' => ''
];
$DB_HOST = [
    'host' => 'localhost',
    'dbname' => 'product-manager',
    'user' => 'zmxnutfi_user',
    'pass' => 'X3RucMqpOt@['
];
$whitelist = array(
    '127.0.0.1',
    '::1',
    'localhost'
);
if (in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
    return $DB_LOCAL;
} else {
    return $DB_HOST;
}

?>