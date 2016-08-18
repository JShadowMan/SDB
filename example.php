<?php
# Import SDB Helper Class
use SDB\Helper;
use SDB\Expression;

# Register autoload function
if (function_exists('spl_autoload_register')) {
    spl_autoload_register(function($class) {
        require_once str_replace(array('\\'), '/', str_replace('SDB', 'src', $class)) . '.php';
    });
}

Helper::server('127.0.0.1', 3306, 'root', 'root', 'here');

$helper = new Helper('here_', Helper::ADAPTER_PDO_MYSQL);

echo "<pre>";

//print_r($helper->select()->from('table.options')->__toString());

//print_r($helper->select()->from('table.options')->rows(array('name' => 'pageSize', 'value' => '22')));

// print_r($helper->insert('table.options')->keys('name', 'value')->values(
//             array('1', '2'),
//             array('3', '4'),
//             array('1', Helper::DATA_NULL),
//             array(Helper::DATA_NULL, Helper::DATA_DEFAULT)
//         )->page(11, 10)
//);

print_r($helper->select()->from('table.options')
        ->where(Expression::equal('for', '0'))
        ->join(array('table.user', 'table.articles'))
        ->on(Expression::equal('table.options.for', 'table.user.uid'))
        ->on(Expression::equal('table.options.for', 'table.articles.pid'))
        ->join('table.comment', Helper::JOIN_LEFT)
        ->on(Expression::equal('table.comment.pid', 'table.articles.pid'))
        ->having(Expression::equal('table.user.uid', 0, 'si'))
);

echo "</pre>";
