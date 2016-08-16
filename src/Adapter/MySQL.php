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

?>