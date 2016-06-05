<?php
if (function_exists('spl_autoload_register')) {
    spl_autoload_register(function($class) {
        require_once str_replace(array('\\'), '/', str_replace('Db', 'src', $class)) . '.php';
    });
}

use Db\Helper;

// Helper::server('localhost', '3306', 'root', 'root', 'test');

$helper = new Helper(Helper::DB_ADAPTER_MYSQL, 'table_');

// var_dump($helper->select()->__toString());
var_dump($helper->getServer());