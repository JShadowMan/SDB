<?php
namespace Db\Adapter;

/**
 *
 * @author ShadowMan
 */

class Adapter_MySQL implements \Db\_Abstract\Abstract_Adapter {
    /**
     * Check MySQL Server
     * 
     * @see \Db\_Abstract\Abstract_Adapter::isAvaliable()
     */
    public static function isAvaliable() {
        return class_exists('mysqli') ? true : function_exists('mysqli_connect') ? true : false ;
    }

    
}

?>