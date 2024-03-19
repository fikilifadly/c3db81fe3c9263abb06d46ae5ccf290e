<?php

function load_core($class)
{
    $path = __DIR__ . '/' . $class . '.php';
    // echo $path;
    if (file_exists($path)) {
        require_once $path;
    }
}

spl_autoload_register('load_core');
