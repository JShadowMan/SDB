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

    public function __construct(&$adapterInstance) {
        if ($adapterInstance instanceof Adapter) {
            $this->_adapterInstance = $adapterInstance;
        } else {
            throw new \Exception('SDB: adapter invalid in Query Constructor', 1996);
        }
    }

    /**
     * SQL Basic Syntax: SELECT
     * 
     * @param array $fields
     * @return \SDB\Query
     */
    public function select(array $fields) {
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
     * 
     * @param string $table
     */
    public function update($table) {
        $this->_table = $table;
    }

    public function from($table, $singleTable = true) {
        
    }
}

?>