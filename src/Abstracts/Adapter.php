<?php
/**
 * @package SDB
 * @author  ShadowMan
 */
namespace SDB\Abstracts;

abstract class Adapter {
    /**
     * Is adapter avaliable
     * 
     * @return boolean
     */
    abstract public function avaliable();

    /**
     * execute failter for table name
     * 
     * @param string $table
     * @return string
     */
    abstract public function tableFilter($table);

    /**
     * based preBuilder generate SELECT syntax
     * 
     * @param array $preBuilder
     * @return string
     */
    abstract public function parseSelect($preBuilder);

    /**
     * based preBuilder generate UPDATE syntax
     * 
     * @param string $preBuilder
     * @return string
     */
    abstract public function parseUpdate($preBuilder);

    /**
     * based preBuilder generate INSERT syntax
     * 
     * @param string $preBuilder
     * @return string
     */
    abstract public function parseInsert($preBuilder);

    /**
     * based preBuilder generate DELETE syntax
     *
     * @param string $preBuilder
     * @return string
     */
    abstract public function parseDelete($preBuilder);

    /**
     * quoted identifiers
     * 
     * @param string $string
     */
    abstract public function quoteKey($string);

    /**
     * quoted identifiers
     * 
     * @param string $string
     */
    abstract public function quoteValue($string);

    /**
     * table prefix
     * 
     * @var string
     */
    protected $_tablePrefix = null;

    /**
     * default constructor
     * 
     * @param string $tablePrefix
     */
    public function __construct($tablePrefix) {
        $this->_tablePrefix = is_string($tablePrefix) ? $tablePrefix : strval($tablePrefix);
    }
}

?>