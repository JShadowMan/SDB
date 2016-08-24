<?php
/**
 * SDB Helper Test Suite
 * 
 * @package SDB
 * @author  ShadowMan
 */
use SDB\Helper;

class HelperTest extends \PHPUnit_Framework_TestCase {
    public function setUp() {
        Helper::server('127.0.0.1', 3306, 'root', '', 'test');
    }

    /**
     * @expectedException Exception
     */
    public function testInvalidAdapter() {
        $instance = new Helper('table_', 'INVALID_ADAPTER');
    }

    /**
     * @expectedException Exception
     */
    public function testInvalidTablePrefix() {
        $instance = new Helper(0000, Helper::ADAPTER_MYSQL);
    }

    public function testUsingDefaultAdapter() {
        if (!extension_loaded('mysqli')) {
            $this->expectException('Exception');
        }
        $instance = new Helper('table_');
    }

    public function testUnavaliableAdapter() {
        if (!extension_loaded('pdo_mysql')) {
            $instance = new Helper('table_', Helper::ADAPTER_PDO_MYSQL);
        }
    }

    public function testDisableStrictMode() {
        Helper::disableStrictMode();
    }

    public function testAddServer() {
        Helper::server('127.0.0.1', 3306, 'root', 'root', 'here');
    }

    public function testBuilderReturnType() {
        $instance = new Helper('table_');

        $this->assertInstanceOf('SDB\\Query', $instance->builder());
    }

    public function testBuildSelectReturnType() {
        $instance = new Helper('table_');

        $this->assertInstanceOf('SDB\\Query', $instance->select('fields'));
    }

    public function testBuildUpdateReturnType() {
        $instance = new Helper('table_');

        $this->assertInstanceOf('SDB\\Query', $instance->update('table'));
    }

    public function testBuildInsertReturnType() {
        $instance = new Helper('table_');

        $this->assertInstanceOf('SDB\\Query', $instance->insert('table'));
    }

    public function testBuildDeletReturnType() {
        $instance = new Helper('table_');

        $this->assertInstanceOf('SDB\\Query', $instance->delete('table'));
    }

    public function testQuery() {
        $instance = new Helper('table_');

        $instance->query('SELECT 1');
    }

    public function testFetchAssoc() {
        $instance = new Helper('table_');

        $instance->query('SELECT 1, 2');
        $this->assertEquals(array('1' => '1', '2' => '2'), $instance->fetchAssoc());
    }

    public function testFetchAll() {
        $instance = new Helper('table_');

        $instance->query('SELECT 1');
        $this->assertEquals(array(array('1' => '1')), $instance->fetchAll());
    }
}
