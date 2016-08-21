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
    abstract public function connect($host, $port, $user, $password, $database, $charset = 'utf8');

    /**
     * return database information
     */
    abstract public function serverInfo();

    /**
     * return last insert row id
     */
    abstract public function laseInsertId();

    /**
     * return last query affected rows
     */
    abstract public function affectedRows();

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
    abstract public function parseSelect($preBuilder, $table);

    /**
     * based preBuilder generate UPDATE syntax
     * 
     * @param string $preBuilder
     * @return string
     */
    abstract public function parseUpdate($preBuilder, $table);

    /**
     * based preBuilder generate INSERT syntax
     * 
     * @param string $preBuilder
     * @return string
     */
    abstract public function parseInsert($preBuilder, $table);

    /**
     * based preBuilder generate DELETE syntax
     *
     * @param string $preBuilder
     * @return string
     */
    abstract public function parseDelete($preBuilder, $table);

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
     * execute query
     * 
     * @param string $query
     * @return bool query state
     */
    abstract public function query($query);

    /**
     * save query data
     * 
     * @var array|object
     */
    protected $_result = null;

    /**
     * result query data internal pointer
     * 
     * @var int
     */
    protected $_resultCurrentIndex = 0;

    /**
     * fetch last query data
     * 
     * @param array $keys
     * @return array
     */
    abstract public function fetchAssoc($keys = null);

    /**
     * fetch last query data
     * 
     * @return array
     */
    abstract public function fetchAll();

    /**
     * reposition rows position indicator
     */
    final public function reset() {
        $this->_resultCurrentIndex = 0;
    }

    /**
     * sets the position indicator associated with the rows to a new position.
     * 
     * @param int $index
     */
    final public function seek($index) {
        if (is_array($this->_result) && count($this->_result) > $index && $index > 0) {
            $this->_resultCurrentIndex = $index;
        }
    }

    /**
     * table prefix
     * 
     * @var string
     */
    protected $_tablePrefix = null;

    /**
     * an object which represents the connection to a MySQL Server.
     * 
     * @var mixed
     */
    protected $_instance = null;

    /**
     * is connect
     * 
     * @var boolean
     */
    protected $_connectFlag = false;

    /**
     * default constructor
     * 
     * @param string $tablePrefix
     */
    public function __construct($tablePrefix) {
        $this->_tablePrefix = is_string($tablePrefix) ? $tablePrefix : strval($tablePrefix);
    }
}
