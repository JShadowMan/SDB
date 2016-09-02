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
    protected $_affectedRows = null;

    /**
     * PDO Version
     * 
     * @see \SDB\Adapter\MySQL::avaliable()
     */
    public function avaliable() {
        return extension_loaded('pdo_mysql');
    }

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
        if (is_string($query) && strlen($query)) {
            $result = $this->_instance->query($query, \PDO::FETCH_ASSOC);

            if ($this->_instance->errorCode() != '00000') {
                throw new \Exception("SDB: MySQL: {$this->_instance->errorCode()}: {$this->_instance->errorInfo()}", 1996);
            }

            if ($result instanceof \PDOStatement) {
                if ($result->errorCode() !== '00000') {
                    throw new \Exception("SDB: MySQL: {$result->errorCode()}: {$result->errorInfo()}", 1996);
                }

                foreach ($result as $row) {
                    $this->_result[] = $row;
                }

                $this->_affectedRows = $result->rowCount();
            }
        }

        return true;
    }

    /**
     * PDO Version
     * 
     * @see \SDB\Adapter\MySQL::serverInfo()
     */
    public function serverInfo() {
        if ($this->_connectFlag == false && $this->_instance == null) {
            throw new \Exception('SDB: Required connect first', 1996);
        }

        return null; # PDO does't provied
    }

    /**
     * PDO Version
     * 
     * @see \SDB\Adapter\MySQL::affectedRows()
     */
    public function affectedRows() {
        return $this->_affectedRows;
    }

    /**
     * PDO Version
     * 
     * @see \SDB\Adapter\MySQL::lastInsertId()
     */
    public function lastInsertId() {
        return $this->_instance->lastInsertId();
    }
}
