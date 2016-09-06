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
        'fields' => array(),
        'rows'   => array('keys' => array(), 'values' => array()),
        'order'  => array(),
        'where'  => array(),
        'group'  => array(),
        'join'   => array(),
        'on'     => array(),
        'having' => array(),
        'set'    => array(),
        'using'  => null,
        'limit'  => null,
        'offset' => null,
        'insertSelect' => null
    );

    /**
     * adapter instance, reference
     * 
     * @var SDB\Abstracts\Adapter
     */
    private $_adapterInstance = null;

    /**
     * helper instance
     * 
     * @var Helper
     */
    private $_helperInstance = null;

    /**
     * default table name
     *
     * @var string
     */
    private $_table = null;

    private $_singleTableMode = true;

    private $_queryAction = null;

    public function __construct(&$adapterInstance, &$helperInstance) {
        if ($adapterInstance instanceof Adapter) {
            $this->_adapterInstance = $adapterInstance;
        }

        if ($helperInstance instanceof Helper) {
            $this->_helperInstance  = $helperInstance;
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
        $this->_table = $this->_adapterInstance->tableFilter(is_string($table) ? $table : strval($table));

        return $this;
    }

    /**
     * SQL Basic Syntax: INSERT
     *
     * @param string $table
     */
    public function insert($table) {
        $this->_queryAction = 'INSERT';
        $this->_table = $this->_adapterInstance->tableFilter(is_string($table) ? $table : strval($table));

        return $this;
    }

    public function insertSelect($query) {
        if (!($query instanceof Query)) {
            throw new \Exception('SDB: Query: insertSelect params invalid', 1996);
        }
        $this->_preBuilder['insertSelect'] = $query;

        return $this;
    }

    /**
     * SQL Basic Syntax: DELETE
     *
     * @param string $table
     */
    public function delete($tables, $using = null) {
        $this->_queryAction = 'DELETE';
        $this->_table = is_string($tables) ? $this->_adapterInstance->tableFilter($tables) :
                ((is_array($tables) ? (array_map(function($t) { return $this->_adapterInstance->tableFilter($t); }, $tables)) :
                        strval($tables)));
        $this->_preBuilder['using'] = ($using === null) ? '' : $this->_adapterInstance->tableFilter(strval($using));

        return $this;
    }

    public function from($table, $singleTable = true) {
        $this->_table = $this->_adapterInstance->tableFilter(is_string($table) ? $table : strval($table));

        return $this;
    }

    public function limit($rows) {
        $this->_preBuilder['limit'] = intval($rows);

        return $this;
    }

    public function offset($offset) {
        $this->_preBuilder['offset'] = intval($offset);

        return $this;
    }

    public function page($index, $size) {
        $this->offset(intval($size) * (max(intval($index), 1) - 1));
        $this->limit($size);

        return $this;
    }

    public function rows(array $rows) {
        $keys = array_map(function($key) { return $this->_adapterInstance->quoteKey($key); }, array_keys($rows));
        $vals = array_map(function($val) { return $this->_adapterInstance->quoteValue($val); }, array_values($rows));

        if (empty($this->_preBuilder['rows']['keys'])) {
            $this->_preBuilder['rows']['keys'] = $keys;
        }
        $this->_preBuilder['rows']['values'][] = $vals;

        return $this;
    }


    public function keys() {
        if (!empty($this->_preBuilder['rows']['keys'])) {
            return $this;
        }

        $keys = func_get_args();
        foreach ($keys as &$key) {
            $key = $this->_adapterInstance->quoteKey($key);
        }
        $this->_preBuilder['rows']['keys'] = $keys;

        return $this;
    }

    public function values() {
        if (empty($this->_preBuilder['rows']['keys'])) {
            throw new \Exception('SDB: Query: keys not exists', 1996);
        }

        $rows = array_map(function($val) { return is_array($val) ? $val : array($val); }, func_get_args());
        foreach ($rows as &$row) {
            if (count($row) != count($this->_preBuilder['rows']['keys'])) {
                throw new \Exception('SDB: Query: values size and keys not matched', 1996);
            }

            foreach ($row as &$val) {
                if ($val === Helper::DATA_DEFAULT || $val === Helper::DATA_NULL) {
                    $val = stripcslashes($val);
                    continue;
                }
                $val = $this->_adapterInstance->quoteValue($val);
            }
        }
        $this->_preBuilder['rows']['values'] = array_merge($this->_preBuilder['rows']['values'], $rows);

        return $this;
    }

    public function order($field, $sort = Helper::ORDER_ASC) {
        if (!is_string($field) || !in_array($sort, array(Helper::ORDER_DESC, Helper::ORDER_ASC))) {
            throw new \Exception('SDB: Query: in ORDER params invalid', 1996);
        }

        $this->_preBuilder['order'][] = array('field' => $this->_adapterInstance->tableFilter($field), 'sort' => $sort);
        return $this;
    }

    public function where($expression, $conjunction = Helper::CONJUNCTION_AND) {
        if (!($expression instanceof Expression)) {
            throw new \Exception('SDB: Query: in WHERE params invalid', 1996);
        }
        if (!in_array($conjunction, array(Helper::CONJUNCTION_AND, Helper::CONJUNCTION_OR))) {
            throw new \Exception('SDB: Query: conjunction invalid', 1996);
        }

        $this->_preBuilder['where'][] = array_merge(array( 'conjunction' => $conjunction), 
                $expression->expression(array($this->_adapterInstance, 'tableFilter'), array($this->_adapterInstance, 'quoteValue')));
        return $this;
    }

    public function group($field, $sort = Helper::ORDER_ASC) {
        if (!is_string($field) || !in_array($sort, array(Helper::ORDER_DESC, Helper::ORDER_ASC))) {
            throw new \Exception('SDB: Query: in GROUP params invalid', 1996);
        }

        $this->_preBuilder['group'][] = array('field' => $this->_adapterInstance->tableFilter($field), 'sort' => $sort);
        return $this;
    }

    public function join($tables, $references = Helper::JOIN_INNER) {
        if (!in_array($references, array(Helper::JOIN_INNER, Helper::JOIN_LEFT, Helper::JOIN_RIGHT))) {
            throw new \Exception('SDB: Query: in JOIN params invalid', 1996);
        }

        // TODO. A table reference can be aliased using tbl_name AS alias_name or tbl_name alias_name
        if (is_string($tables)) {
            $tables = array($this->_adapterInstance->tableFilter($tables));
        } else if (is_array($tables)) {
            $tables = array_map(function($val) { return (is_string($val)) ? $this->_adapterInstance->tableFilter($val) : null; }, $tables);
        }

        $this->_preBuilder['join'][] = array('references' => $references, 'tables' => $tables);
        return $this;
    }

    public function on($expression, $conjunction = Helper::CONJUNCTION_AND) {
        if (!($expression instanceof Expression)) {
            throw new \Exception('SDB: Query: in ON params invalid', 1996);
        }

        if (!in_array($conjunction, array(Helper::CONJUNCTION_AND, Helper::CONJUNCTION_OR))) {
            throw new \Exception('SDB: Query: conjunction invalid', 1996);
        }

        $lval = explode('.', $expression->lval(array($this->_adapterInstance, 'tableFilter')));
        $rval = explode('.', $expression->rval(array($this->_adapterInstance, 'tableFilter')));
        $references = array(
            'lval' => array('table' => $lval[0], 'field' => $lval[1]),
            'rval' => array('table' => $rval[0], 'field' => $rval[1])
        );

        $this->_preBuilder['on'][] = array_merge($references, array('operator' => $expression->operator(), 'conjunction' => $conjunction));
        return $this;
    }

    public function having($expression, $conjunction = Helper::CONJUNCTION_AND) {
        if (!($expression instanceof Expression)) {
            throw new \Exception('SDB: Query: in HAVING params invalid', 1996);
        }
        if (!in_array($conjunction, array(Helper::CONJUNCTION_AND, Helper::CONJUNCTION_OR))) {
            throw new \Exception('SDB: Query: conjunction invalid', 1996);
        }

        $this->_preBuilder['having'][] = array_merge(array( 'conjunction' => $conjunction),
                $expression->expression(array($this->_adapterInstance, 'tableFilter'), array($this->_adapterInstance, 'quoteValue')));
        return $this;
    }

    public function set(array $kvps) {
        foreach ($kvps as $key => $value) {
            if (!is_string($key)) {
                throw new \Exception('SDB: Query: set params invalid', 1996);
            }

            if (in_array($value, array(Helper::DATA_DEFAULT, Helper::DATA_NULL))) {
                $this->_preBuilder['set'] = array_merge($this->_preBuilder['set'], 
                        array($this->_adapterInstance->quoteKey($key) => stripcslashes($value)));
            } else {
                $this->_preBuilder['set'] = array_merge($this->_preBuilder['set'],
                        array($this->_adapterInstance->quoteKey($key) => $this->_adapterInstance->quoteValue($value)));
            }
        }

        return $this;
    }

    public function __toString() {
        switch ($this->_queryAction) {
            case 'SELECT': return $this->_adapterInstance->parseSelect($this->_preBuilder, $this->_table); break;
            case 'UPDATE': return $this->_adapterInstance->parseUpdate($this->_preBuilder, $this->_table); break;
            case 'INSERT': return $this->_adapterInstance->parseInsert($this->_preBuilder, $this->_table); break;
            case 'DELETE': return $this->_adapterInstance->parseDelete($this->_preBuilder, $this->_table); break;
            default: throw new \Exception('SDB: Query: unknown query action or undefined action', 1996);
        }
    }

    public function query() {
        $this->_helperInstance->connect();

        return $this->_adapterInstance->query($this->__toString());
    }
}
