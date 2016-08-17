<?php
/**
 * PDO MySQL Adapter
 * 
 * @author  ShadowMan
 * @package SDB
 */
namespace SDB\Adapter;

use SDB\Adapter\MySQL;

class PDO_MySQL extends MySQL {
    /**
     * PDO Version
     * 
     * @see \SDB\Adapter\MySQL::connect()
     */
    public function connect($host, $port, $user, $password, $database, $charset = 'utf8') {
        $charset = strtoupper($charset);
        if ($this->_connectFlag == false && $this->_instance == null) {
            $this->_instance = @new \PDO("mysql:host={$host};port={$port};dbname={$database};charset={$charset}", $user, $password);

            $this->_connectFlag = true;
        }
    }

    /**
     * PDO Version
     * 
     * @see \SDB\Adapter\MySQL::query()
     */
    public function query($query) {
        
    }
}
