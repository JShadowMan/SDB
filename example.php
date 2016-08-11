<?php
# Import SDB Helper Class
use SDB\Helper;

# Register autoload function
if (function_exists('spl_autoload_register')) {
    spl_autoload_register(function($class) {
        require_once str_replace(array('\\'), '/', str_replace('SDB', 'src', $class)) . '.php';
    });
}

Helper::server('127.0.0.1', 3306, 'root', 'root', 'here');

$helper = new Helper(Helper::ADAPTER_MYSQL, 'here_');