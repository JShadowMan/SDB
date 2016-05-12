<?php
/**
 * Introduction
 * 
 * @package Db
 * @author  ShadowMan
 * @license MIT License
 */

class HelperTest extends \PHPUnit_Framework_TestCase {
    public static function helper($adapter = \Db\Helper::DB_ADAPTER_MYSQL, $prefix = null) {
        return new \Db\Helper($adapter, $prefix);
    }

    public function testServer() {
        $this->assertEquals('SERVER', \Db\Helper::server());
    }

    public function testDefaultClass() {
        $helper = self::helper();
    
        $this->assertEquals(true, $helper->getAdapter() instanceof \Db\Adapter\Adapter_MySQL);
        $this->assertEquals(null, $helper->getPrefix());
    }

    public function testCustomClass() {
        $helper = self::helper(\Db\Helper::DB_ADAPTER_ORACLE, 'table_');

        $this->assertEquals(false, $helper->getAdapter() instanceof \Db\_Abstract\Abstract_Adapter);
        $this->assertEquals('table_', $helper->getPrefix());
    }
}

?>