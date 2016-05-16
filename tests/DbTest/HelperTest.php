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
    public static function helper($adapter = Helper::DB_ADAPTER_MYSQL, $prefix = 'table_') {
        Helper::server('localhost', '3306', 'root', 'root', 'test');
        return Helper::factory($adapter, $prefix);
    }

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

    public function testMutliServer() {
        
    }

    public function testDefaultUInstance() {
        $helper = Helper::factory(Helper::DB_ADAPTER_MYSQL, 'table_');

        $this->assertEquals(true, $helper->getAdapter() instanceof \Db\Adapter\Adapter_MySQL);
        $this->assertEquals('table_', $helper->getPrefix());
    }

    public function testFactoryWithConnect() {
//         $helper = \Db\Helper::factory(\Db\Helper::DB_ADAPTER_MYSQL, 'table_', true);
    }

    /**
     * @expectedException Exception
     */
    public function testCustomClass() {
        $helper = self::helper(Helper::DB_ADAPTER_ORACLE);

        $this->assertEquals('table_', $helper->getPrefix());
    }

    public function testAction() {
        $helper = self::helper();

        $helper->select();
        $this->assertEquals(Helper::DB_OPERATOR_SELECT, $helper->action());
    }

    public function testCleanPool() {
        $helper = self::helper();

        $helper->select();
        $helper->update('table.user');
        $helper->insert('table.user');
        $this->assertEquals(Helper::DB_OPERATOR_INSERT, $helper->action());

        $helper->cleanPool();
        $this->assertEquals(null, $helper->action());
    }

    public function testSelect() {
        
    }

    public function testUpdate() {
        
    }

    public function testInsert() {
        
    }

    public function testDelete() {
        
    }
}

?>