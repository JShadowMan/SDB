<?php
/**
 * @package SDB
 * @author  ShadowMan
 */
namespace SDB\Adapter;
use SDB\Abstracts\Adapter;

class MySQL implements Adapter {
    public static function avaliable() {
        return class_exists('mysqli') ? true :
            (function_exists('mysqli_connect') ? true :
                (function_exists('mysql_connect') ? true :
                    false));
    }
}

?>