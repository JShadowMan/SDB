<?php
/**
 * SDB Helper Test Suite
 * 
 * @package SDB
 * @author  ShadowMan
 */
use SDB\Helper;

class HelperTest extends \PHPUnit_Framework_TestCase {
    public function instance($adapter, $tablePrefix) {
        Helper::server('127.0.0.1', 3306, 'root', 'root', 'here');

        return new Helper($adapter, $tablePrefix);
    }

    public function testServer() {
        Helper::server('127.0.0.1', 3306, 'root', 'root', 'here');
    }

    /**
     * @expectedException Exception
     */
    public function testInvalidAdapter() {
        $this->instance('INVALID_ADAPTER', 'here_');
    }

    
}

?>