<?php

namespace Db\Adapter;

use Db\_Abstract\Abstract_Adapter;

/**
 *
 * @author ShadowMan
 */
class Adapter_MySQL implements Abstract_Adapter {
    /**
     * mysqli instance, require enable php_mysqli extension
     * 
     * @var \mysqli
     */
    private $_instace = null;

    private $_server = array();

    /**
     * (non-PHPdoc)
     *
     * @see \Db\_Abstract\Abstract_Adapter::isAvaliable()
     *
     */
    public static function isAvaliable() {
        return class_exists('mysqli') ? true : false;
    }

    /**
     * 
     * @param string $prefix
     */
    function __construct($server) {
        if (empty($server)) {
            throw new \Exception('Did not add Server', 2);
        }
        $this->_server = $server;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Db\_Abstract\Abstract_Adapter::connect()
     *
     */
    public function connect() {
        if (count($this->_server) != 6) {
            throw new \Exception('Did not add Server', 2);
        }

        $this->_instace = @new \mysqli($this->_server['host'], $this->_server['user'], $this->_server['password'],
                $this->_server['database'], $this->_server['port']);
        if ($this->_instace->connect_errno != 0) {
            $this->_instace = null;

            throw new \Exception($this->_instace->error, $this->_instace->connect_errno);
        }
    }

    private function selfConnect() {
        if (count($this->_server) != 6) {
            return null;
        }

        $this->_instace = @new \mysqli($this->_server['host'], $this->_server['user'], $this->_server['passwrod'],
                $this->_server['database'], $this->_server['port']);
        if ($this->_instace->connect_errno != 0) {
            $this->_instace = null;

            throw new \Exception($this->_instace->error, $this->_instace->connect_errno);
        }
        return $this->_instace;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Db\_Abstract\Abstract_Adapter::serverInfo()
     *
     */
    public function serverInfo() {
        return ($this->_instace) ? $this->_instace->server_info : null;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Db\_Abstract\Abstract_Adapter::lastInsertId()
     *
     */
    public function lastInsertId() {
        return ($this->_instace) ? $this->_instace->insert_id : null;
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Db\_Abstract\Abstract_Adapter::query()
     *
     */
    public function query($query) {
        if ($this->_instace == null) {
            if (self::selfConnect() == null) {
                throw new \Exception('Did not add Server', 2);
            }
        }
    }

    public static function tableFilter($table, $prefix = null) {
        return '`' . ((strpos($table, 'table.') === 0) ? substr_replace($table, $prefix, 0, 6) : $table) . '`';
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Db\_Abstract\Abstract_Adapter::escapeValue()
     *
     */
    public static function escapeValue($value) {
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Db\_Abstract\Abstract_Adapter::escapeKey()
     *
     */
    public static function escapeKey($key) {
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Db\_Abstract\Abstract_Adapter::parseSelect()
     *
     */
    public static function parseSelect($preBuilder) {
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Db\_Abstract\Abstract_Adapter::parseUpdate()
     *
     */
    public static function parseUpdate($preBuilder) {
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Db\_Abstract\Abstract_Adapter::parseInsert()
     *
     */
    public static function parseInsert($preBuilder) {
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Db\_Abstract\Abstract_Adapter::parseChange()
     *
     */
    public static function parseChange($preBuilder) {
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Db\_Abstract\Abstract_Adapter::fetch()
     *
     */
    public function fetch($instace, $result) {
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Db\_Abstract\Abstract_Adapter::parseDelete()
     *
     */
    public static function parseDelete($preBuilder) {
    }
}

?>