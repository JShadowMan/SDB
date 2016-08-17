<?php
/**
 * SDB Helper Test Suite
 * 
 * @package SDB
 * @author  ShadowMan
 */
use SDB\Helper;

class HelperTest extends \PHPUnit_Framework_TestCase {
    public function instance($tablePrefix, $adapter = null) {
        Helper::disableStrictMode();
        Helper::server('127.0.0.1', 3306, 'root', 'root', 'here');

        return new Helper($tablePrefix, $adapter);
    }

    /**
     * 
     */
    public function testServer() {
        Helper::server('127.0.0.1', 3306, 'root', 'root', 'here');
    }

    /**
     * @expectedException Exception
     */
    public function testInvalidAdapter() {
        $this->instance('here_', 'INVALID_ADAPTER');
    }
}

?>