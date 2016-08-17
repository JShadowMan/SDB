<?php
/**
 * SDB Query Class
 * @package SDB
 * @author  ShadowMan
 */
namespace SDB;

use SDB\Abstracts\Adapter;

class Query {
    /**
     * pre builder
     * 
     * @var array
     */
    private $_preBuilder = array(
        'fields' => array()
    );

    /**
     * adapter instance, reference
     * 
     * @var SDB\Abstracts\Adapter
     */
    private $_adapterInstance = null;

    /**
     * default table name
     *
     * @var string
     */
    private $_table = null;

    private $_singleTableMode = true;

    private $_queryAction = null;

    public function __construct(&$adapterInstance) {
        if ($adapterInstance instanceof Adapter) {
            $this->_adapterInstance = $adapterInstance;
        } else {
            throw new \Exception('SDB: Query: adapter invalid in Query Constructor', 1996);
        }
    }

    /**
     * SQL Basic Syntax: SELECT
     * 
     * @param array $fields
     * @return \SDB\Query
     */
    public function select(array $fields) {
        $this->_queryAction = 'SELECT';

        if (empty($fields)) {
            $this->_preBuilder['fields'][] = '*';
        } else {
            foreach ($fields as &$field) {
                if (is_array($field)) {
                    $field[0] = $this->_adapterInstance->tableFilter($field[0]);
                    $field[1] = $this->_adapterInstance->quoteKey($field[1]);
                } else {
                    // check is full field name, like table.articles.contents
                    if (strpos($field, 'table.') === 0) {
                        $field = $this->_adapterInstance->tableFilter($field);
                    } else {
                        $field = $this->_adapterInstance->quoteKey($field);
                    }
                }
            }

            $this->_preBuilder['fields'] = array_merge($this->_preBuilder['fields'], $fields);
        }

        return $this;
    }

    /**
     * SQL Basic Syntax: UPDATE
     * 
     * @param string $table
     */
    public function update($table) {
        $this->_queryAction = 'UPDATE';
        $this->_table = is_string($table) ? $table : strval($table);

        return $this;
    }

    /**
     * SQL Basic Syntax: INSERT
     *
     * @param string $table
     */
    public function insert($table) {
        $this->_queryAction = 'INSERT';
        $this->_table = is_string($table) ? $table : strval($table);

        return $this;
    }

    /**
     * SQL Basic Syntax: DELETE
     *
     * @param string $table
     */
    public function delete($table) {
        $this->_queryAction = 'DELETE';
        $this->_table = is_string($table) ? $table : strval($table);

        return $this;
    }

    public function from($table, $singleTable = true) {
        $this->_table = is_string($table) ? $table : strval($table);

        return $this;
    }

    public function __toString() {
        switch ($this->_queryAction) {
            case 'SELECT': return $this->_adapterInstance->parseSelect($this->_preBuilder); break;
            case 'UPDATE': return $this->_adapterInstance->parseUpdate($this->_preBuilder); break;
            case 'INSERT': return $this->_adapterInstance->parseInsert($this->_preBuilder); break;
            case 'DELETE': return $this->_adapterInstance->parseDelete($this->_preBuilder); break;
            default: throw new \Exception('SDB: Query: unknown query action or undefined action', 1996);
        }
    }
}
