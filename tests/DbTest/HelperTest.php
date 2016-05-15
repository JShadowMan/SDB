<?php
use Db\Helper;
/**
 * Introduction
 * 
 * @package Db
 * @author  ShadowMan
 * @license MIT License
 */

class HelperTest extends \PHPUnit_Framework_TestCase {
    public function testServer() {
        Helper::server('localhost', '3306', 'root', 'root', 'test');
        $server = Helper::getServer();
        $this->assertEquals('localhost', $server['host']);
        $this->assertEquals('test', $server['database']);

        Helper::server('1.2.3.4', '3306', 'user', 'root', 'test');
        $server = Helper::getServer();
        $this->assertEquals('1.2.3.4', $server['host']);
        $this->assertEquals('user', $server['user']);
    }

    public function testDefaultClass() {
        $helper = \Db\Helper::factory(\Db\Helper::DB_ADAPTER_MYSQL, 'table_');

        $this->assertEquals(true, $helper->getAdapter() instanceof \Db\Adapter\Adapter_MySQL);
        $this->assertEquals('table_', $helper->getPrefix());
    }

    /**
     * @expectedException Exception
     */
    public function testCustomClass() {
        $helper = \Db\Helper::factory(\Db\Helper::DB_ADAPTER_ORACLE, 'table_');

        $this->assertEquals('table_', $helper->getPrefix());
    }
}

?>