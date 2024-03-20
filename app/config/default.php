<?php
$file = "/var/www/html/config/config.properties";

if (!file_exists($file)) {
    echo "File config.properties not found";
    exit();
}

$properties = parse_ini_file($file);
define('DB_HOST', $properties['host']);
define('DB_USER', $properties['user']);
define('DB_PASS', $properties['password']);
define('DB_NAME', $properties['database']);
