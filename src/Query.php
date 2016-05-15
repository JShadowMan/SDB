<?php

namespace Db;

/**
 *
 * @author ShadowMan
 */
class Query {
    private $_preBuilder = array();

    /**
     * 
     * @var \Db\_Abstract\Abstract_Adapter
     */
    private $_adapter = null;

    private $_action = null;

    function __construct(&$adapter, $prefix = null) {
        $this->_adapter = $adapter;
        $this->_preBuilder = array(
            'table'  => null,
            'rows'   => array('keys' => array(), 'values' => array()),
            'fields' => array(),
            'join'   => array(),
            'on'     => array(),
            'group'  => array(),
            'having' => array(),
            'where'  => array(),
            'order'  => array(),
            'limit'  => null,
            'offset' => null
        );
    }

    private function escapeField($column) {
        return $this->_adapter->escapeKey($column);
    }

    private function tableFilter($table) {
        return '`' . ((strpos($table, 'table.') === 0) ? substr_replace($table, $this->_prefix, 0, 6) : $table) . '`';
    }

    public function select($fields) {
        $this->_action = Helper::DB_OPERATOR_SELECT;
        $this->_preBuilder['fields'] = is_array($fields) ? array_map(function($field) {
            return self::escapeField($field);
        }, $fields) : array( '*' );

        return $this;
    }

    public function table($table) {
        $this->_preBuilder['table'] = self::tableFilter($table);

        return $this;
    }

    public function update($table) {
        $this->_action = Helper::DB_OPERATOR_UPDATE;
        $this->_preBuilder['table'] = self::tableFilter($table);

        return $this;
    }

    public function insert($table) {
        $this->_action = Helper::DB_OPERATOR_INSERT;
        $this->_preBuilder['table'] = self::tableFilter($table);

        return $this;
    }

    public function delete($table) {
        $this->_action = Helper::DB_OPERATOR_DELETE;
        $this->_preBuilder['table'] = self::tableFilter($table);

        return $this;
    }

    public function change($table) {
        
    }

    /**
     * define keys 
     * 
     * @param array $keys
     */
    public function keys(array $keys) {
        
    }

    public function values() {
        
    }

    public function action() {
        return $this->_action;
    }

    public function __toString() {
        var_dump($this->_preBuilder, $this->_action);
        switch ($this->_action) {
            case Helper::DB_OPERATOR_SELECT: return $this->_adapter->parseSelect($this->_preBuilder);
            case Helper::DB_OPERATOR_UPDATE: return $this->_adapter->parseUpdate($this->_preBuilder);
            case Helper::DB_OPERATOR_INSERT: return $this->_adapter->parseInsert($this->_preBuilder);
            case Helper::DB_OPERATOR_DELETE: return $this->_adapter->parseDelete($this->_preBuilder);
        }
    }
}

?>