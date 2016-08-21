<?php
/**
 * @package SDB
 * @author  ShadowMan
 */
namespace SDB\Adapter;

use SDB\Abstracts\Adapter;
use SDB\Query;

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
    public function parseSelect($preBuilder, $table) {
        $sql = 'SELECT ';
        $sql .= implode(', ', $this->parseField($preBuilder['fields']));
        $sql .= ' FROM ' . $table;

        # JOIN
        if (!empty($preBuilder['join'])) {
            foreach ($preBuilder['join'] as $row) {
                $sql .= " {$row['references']} ( " . implode(', ', $row['tables']) . " )";
            }
        }

        # ON
        if (!empty($preBuilder['on'])) {
            $sql .= " ON ( " . self::parseON($preBuilder['on']) . " )";
        }

        # WHERE
        if (!empty($preBuilder['where'])) {
            $sql .= ' WHERE ';
            foreach ($preBuilder['where'] as $row) {
                $sql .= "{$row['lval']} {$row['operator']} {$row['rval']} {$row['conjunction']} ";
            }

            if ($sql[strlen($sql) - 2] == 'R') {
                $sql = substr($sql, 0, strlen($sql) - 4);
            } else {
                $sql = substr($sql, 0, strlen($sql) - 5);
            }
        }

        # GROUP
        if (!empty($preBuilder['group'])) {
            $sql .= ' GROUP BY ';
            foreach ($preBuilder['group'] as $row) {
                $sql .= "{$row['field']} {$row['sort']}, ";
            }
            $sql = substr($sql, 0, strlen($sql) - 2);
        }

        # HAVING
        if (!empty($preBuilder['having'])) {
            $sql .= ' HAVING ';
            foreach ($preBuilder['having'] as $row) {
                $sql .= "{$row['lval']} {$row['operator']} {$row['rval']} {$row['conjunction']} ";
            }

            if ($sql[strlen($sql) - 2] == 'R') { # $conjunction = OR
                $sql = substr($sql, 0, strlen($sql) - 4);
            } else { # $conjunction = AND
                $sql = substr($sql, 0, strlen($sql) - 5);
            }
        }

        # ORDER
        if (!empty($preBuilder['order'])) {
            $sql .= ' ORDER BY ';
            foreach ($preBuilder['order'] as $row) {
                $sql .= "{$row['field']} {$row['sort']}, ";
            }
            $sql = substr($sql, 0, strlen($sql) - 2);
        }

        # LIMIT
        if ($preBuilder['limit'] != null) {
            $sql .= " LIMIT {$preBuilder['limit']}";
        }

        # OFFSET
        if ($preBuilder['offset'] != null) {
            $sql .= " OFFSET {$preBuilder['offset']}";
        }

        return $sql;
    }

    /**
     * based preBuilder generate UPDATE syntax
     *
     * @param string $preBuilder
     * @return string
    */
    public function parseUpdate($preBuilder, $table) {
        $sql = 'UPDATE ';
        $sql .= $table;

        $sql .= ' SET ';
        foreach ($preBuilder['set'] as $key => $value) {
            $sql .= "{$key} = {$value}, ";
        }
        $sql = substr($sql, 0, strlen($sql) - 2);

        if (!empty($preBuilder['where'])) {
            $sql .= ' WHERE ';
            foreach ($preBuilder['where'] as $row) {
                $sql .= "{$row['lval']} {$row['operator']} {$row['rval']} {$row['conjunction']} ";
            }

            if ($sql[strlen($sql) - 2] == 'R') {
                $sql = substr($sql, 0, strlen($sql) - 4);
            } else {
                $sql = substr($sql, 0, strlen($sql) - 5);
            }
        }

        if (!empty($preBuilder['order'])) {
            $sql .= ' ORDER BY ';
            foreach ($preBuilder['order'] as $row) {
                $sql .= "{$row['field']} {$row['sort']}, ";
            }
            $sql = substr($sql, 0, strlen($sql) - 2);
        }

        if ($preBuilder['limit'] != null) {
            $sql .= " LIMIT {$preBuilder['limit']}";
        }

        if ($preBuilder['offset'] != null) {
            $sql .= " OFFSET {$preBuilder['offset']}";
        }

        return $sql;
    }

    /**
     * based preBuilder generate INSERT syntax
     *
     * @param string $preBuilder
     * @return string
    */
    public function parseInsert($preBuilder, $table) {
        $sql = 'INSERT INTO ';
        $sql .= $table;

        if (!empty($preBuilder['rows']['keys'])) {
            $sql .= ' ( ' . implode(', ', array_values($preBuilder['rows']['keys'])) . ' ) ';
        }

        if ($preBuilder['insertSelect'] instanceof Query) {
            $sql .= call_user_func(array($preBuilder['insertSelect'], '__toString'));

            return $sql;
        }

        $sql .= 'VALUES ';
        if (empty(($preBuilder['rows']['values']))) {
            return null;
        } else {
            foreach ($preBuilder['rows']['values'] as $row) {
                $sql .= '( ' . implode(', ', array_values($row)) . ' ), ';
            }
        }
        $sql = substr($sql, 0, strlen($sql) - 2); // Remove unnecessary ', '

        return $sql;
    }

    /**
     * based preBuilder generate DELETE syntax
     *
     * @param string $preBuilder
     * @return string
    */
    public function parseDelete($preBuilder, $table) {
        $sql = 'DELETE FROM ';
        $sql .= is_array($table) ? implode(', ', $table) : $table;

        if (is_array($table)) {
            if ($preBuilder['using'] == null) {
                throw new \Exception('SDB: MySQL: syntax error for multi-table delete', 1996);
            } else {
                $sql .= ' USING ' . $preBuilder['using'];
            }
        }

        if (!empty($preBuilder['join'])) {
            foreach ($preBuilder['join'] as $row) {
                $sql .= " {$row['references']} ( " . implode(', ', $row['tables']) . " )";
            }
        }

        if (!empty($preBuilder['on'])) {
            $sql .= " ON ( " . self::parseON($preBuilder['on']) . " )";
        }

        if (!empty($preBuilder['where'])) {
            $sql .= ' WHERE ';
            foreach ($preBuilder['where'] as $row) {
                $sql .= "{$row['lval']} {$row['operator']} {$row['rval']} {$row['conjunction']} ";
            }

            if ($sql[strlen($sql) - 2] == 'R') {
                $sql = substr($sql, 0, strlen($sql) - 4);
            } else {
                $sql = substr($sql, 0, strlen($sql) - 5);
            }
        }

        if (!empty($preBuilder['order'])) {
            $sql .= ' ORDER BY ';
            foreach ($preBuilder['order'] as $row) {
                $sql .= "{$row['field']} {$row['sort']}, ";
            }
            $sql = substr($sql, 0, strlen($sql) - 2);
        }

        if ($preBuilder['limit'] != null) {
            $sql .= " LIMIT {$preBuilder['limit']}";
        }

        if ($preBuilder['offset'] != null) {
            $sql .= " OFFSET {$preBuilder['offset']}";
        }

        return $sql;
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
                throw new \Exception("SDB: MySQL: {$this->_instance->errno}: {$this->_instance->error}", 1996);
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

        return true;
    }

    /**
     * fetch last query data
     *
     * @param array $keys
     * @return array
     */
    public function fetchAssoc($keys = null) {
        if (is_array($keys)) {
            $values = array();

            foreach ($keys as $key) {
                $values[] = isset($this->_result[$this->_resultCurrentIndex][$key]) ? $this->_result[$this->_resultCurrentIndex][$key] : null;
            }
            $this->_resultCurrentIndex += 1;
            return $values;
        } else if (is_string($keys)) {
            return isset($this->_result[$this->_resultCurrentIndex][$keys]) ? $this->_result[$this->_resultCurrentIndex++][$keys] : null;
        } else {
            return $this->_result[$this->_resultCurrentIndex++];
        }
    }

    /**
     * fetch last query data
     *
     * @return array
     */
    public function fetchAll() {
        return $this->_result;
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

    private function parseField(array $fields) {
        foreach ($fields as &$field) {
            if (is_array($field)) {
                $field = $field[0] . " AS " . $field[1];
            }
        }

        return $fields;
    }

    private function parseON($references) {
        $on = "";

        foreach ($references as $row) {
            $on .= "{$row['lval']['table']}.{$row['lval']['field']} {$row['operator']} {$row['rval']['table']}.{$row['rval']['field']} {$row['conjunction']} ";
        }
        if ($on[strlen($on) - 2] == 'R') { // OR
            $on = substr($on, 0, strlen($on) - 4);
        } else {
            $on = substr($on, 0, strlen($on) - 5);
        }
        return $on;
    }
}
