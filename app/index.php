<?php

if (!session_id()) session_start();

require_once __DIR__ . '/core/Autoload.php';

# untuk menjalankan routes yg didefine
$routes = new Routes();
$routes->run();
