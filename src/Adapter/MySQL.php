<?php
/**
 * @package SDB
 * @author  ShadowMan
 */
namespace SDB\Adapter;

use SDB\Abstracts\Adapter;

class MySQL extends Adapter {
    /**
     * check mysql avaliable
     * 
     * @see \SDB\Abstracts\Adapter::avaliable()
     */
    public function avaliable() {
        return class_exists('mysqli') ? true :
            (function_exists('mysqli_connect') ? true :
                (function_exists('mysql_connect') ? true :
                    false));
    }

    /**
     * connect to database
     *
     * @param string $host
     * @param string|int $port
     * @param string $user
     * @param string $password
     * @param string $database
     * @param string $charset
     * @return boolean connect state
     * @throws \Exception connect error information
     */
    public function connect($host, $port, $user, $password, $database, $charset = 'utf8') {
        if ($this->_connectFlag == false && $this->_instance == null) {
            $this->_instance = @new \mysqli($host, $user, $password, $database, $port);

            if ($this->_instance->connect_errno) {
                throw new \Exception("SDB: MySQL: {$this->_instance->connect_errno}: {$this->_instance->connect_error}", 1996);
            }
            if ($charset != null) {
                $this->_instance->set_charset($charset);
            }

            $this->_connectFlag = true;
        }
    }

    /**
     * return database information
     */
    public function serverInfo() {
        return $this->_instance->server_info;
    }

    /**
     * return last insert row id
     */
    public function laseInsertId() {
        return $this->_instance->insert_id;
    }

    /**
     * return last query affected rows
     */
    public function affectedRows() {
        return $this->_instance->affected_rows;
    }

    /**
     * filter table name
     * 
     * @see \SDB\Abstracts\Adapter::tableFilter()
     */
    public function tableFilter($table) {
        if (strchr($table, '.') === strrchr($table, '.')) {
            return '`' . ((strpos($table, 'table.') === 0) ? substr_replace($table, $this->_tablePrefix, 0, 6) : $table) . '`';
        } else {
            $table = trim(((strpos($table, 'table.') === 0) ? substr_replace($table, $this->_tablePrefix, 0, 6) : $table) . '`', '`');
            return $this->quoteKey($table);
        }
    }

    /**
     * based preBuilder generate SELECT syntax
     *
     * @param array $preBuilder
     * @return string
     */
    public function parseSelect($preBuilder) {
        
    }

    /**
     * based preBuilder generate UPDATE syntax
     *
     * @param string $preBuilder
     * @return string
    */
    public function parseUpdate($preBuilder) {
        
    }

    /**
     * based preBuilder generate INSERT syntax
     *
     * @param string $preBuilder
     * @return string
    */
    public function parseInsert($preBuilder) {
        
    }

    /**
     * based preBuilder generate DELETE syntax
     *
     * @param string $preBuilder
     * @return string
    */
    public function parseDelete($preBuilder) {
        
    }

    /**
     * quoted identifiers
     *
     * @param string $string
     */
    public function quoteKey($string) {
        if (!is_string($string)) {
            $string = strval($string);
        }

        $length = strlen($string);
        $result = '`';
        for ($index = 0; $index < $length; ++$index) {
            $ch = $string[$index];
            if (ctype_alnum($ch) || in_array($ch, array('_', '(', ')', '`'))) {
                $result .= $ch;
            } else if ($ch == '.') {
                $result .= '`.`';
            }
        }
        $result .= '`';

        if ($this->bracketsMatcher($result)) {
            return trim($result, '`');
        }

        return $result;
    }

    /**
     * quoted identifiers
     *
     * @param string $string
    */
    public function quoteValue($string) {
        if (!is_string($string)) {
            $string = strval($string);
        }
        return '\'' . str_replace(array('\'', '\\'), array('\'\'', '\\\\'), $string) . '\'';
    }

    /**
     * execute query
     *
     * @param SDB\Query|string $query
     */
    public function query($query) {
        if (!($this->_instance instanceof \mysqli)) {
            throw new \Exception('SDB: MySQL: required connect frist', 1996);
        }

        if (is_string($query) && strlen($query)) {
            $result = $this->_instance->query($query);

            if ($this->_instance->errno !== 0) {
                throw new \Exception("SDB: MySQL: {$this->_instance->connect_errno}: {$this->_instance->connect_error}", 1996);
            }

            if ($result instanceof \mysqli_result) {
                if (is_array($this->_result)) {
                    array_splice($this->_result, 0, count($this->_result));
                }

                while ($row = $result->fetch_assoc()) {
                    $this->_result[] = $row;
                }

                $result->free();
            }
        }
    }

    /**
     * fetch last query data
     *
     * @param array $keys
     * @return object
     */
    public function fetchObject(array $keys = array()) {
        
    }

    /**
     * fetch last query data
     *
     * @param array $keys
     * @return array
     */
    public function fetchAssoc(array $keys = array()) {
        
    }

    /**
     * fetch last query data
     *
     * @return array
     */
    public function fetchAll() {
        
    }

    private function bracketsMatcher($string) {
        $stack = array();
        $pushFlags = false;

        $string = str_split($string);
        foreach ($string as $ch) {
            if (in_array($ch, array('(', '[', '{'))) {
                $pushFlags = true;
                $stack[] = $ch;
            } else if (in_array($ch, array(')', ']', '}'))) {
                if ((end($stack) == '(' && $ch == ')') ||
                        (end($stack) == '[' && $ch == ']') ||
                        (end($stack) == '{' && $ch == '}')) {
                            array_pop($stack);
                        } else {
                            return false;
                        }
            }
        }

        return empty($stack) && $pushFlags;
    }
}
