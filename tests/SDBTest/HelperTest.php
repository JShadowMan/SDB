<?php
/**
 * SDB Helper Test Suite
 * 
 * @package SDB
 * @author  ShadowMan
 */
use SDB\Helper;
use SDB\Expression;

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
        Helper::server('127.0.0.1', 3306, 'root', '', 'test');
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

        # Insert Test Data
        $scripts = file_get_contents('test.sql', true);
        $scripts = explode(';', $scripts);
        foreach ($scripts as $script) {
            $instance->query($script);
        }
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

    public function testQuerySimpleSelect() {
        $instance = new Helper('table_');

        $instance->query($instance->select()->from('table.users'));
        foreach ($instance->fetchAll() as $rowId => $row) {
            if ($rowId == 0) {
                $this->assertEquals('1', $row['uid']);
                $this->assertEquals('John', $row['name']);
                $this->assertEquals('JohnPassword', $row['password']);
            } else if ($rowId == 1) {
                $this->assertEquals('2', $row['uid']);
                $this->assertEquals('Jack', $row['name']);
                $this->assertEquals('JackPassword', $row['password']);
            }
        }
    }

    public function testQuerySimpleSelectUsingFields() {
        $instance = new Helper('table_');

        $instance->query($instance->select('uid', 'name')->from('table.users'));
        foreach ($instance->fetchAll() as $rowId => $row) {
            if ($rowId == 0) {
                $this->assertEquals('1', $row['uid']);
                $this->assertEquals('John', $row['name']);
            } else if ($rowId == 1) {
                $this->assertEquals('2', $row['uid']);
                $this->assertEquals('Jack', $row['name']);
            }
        }
    }

    public function testQuerySimpleSelectUsingFieldsAlias() {
        $instance = new Helper('table_');

        $instance->query($instance->select(array('uid', 'id'), array('name', 'username'))->from('table.users'));
        foreach ($instance->fetchAll() as $rowId => $row) {
            if ($rowId == 0) {
                $this->assertEquals('1', $row['id']);
                $this->assertEquals('John', $row['username']);
            } else if ($rowId == 1) {
                $this->assertEquals('2', $row['id']);
                $this->assertEquals('Jack', $row['username']);
            }
        }
    }

    public function testQuerySimpleSelectUsingLimit() {
        $instance = new Helper('table_');

        $instance->query($instance->select(array('uid', 'id'), 'name')->from('table.users')->limit(1));

        $result = $instance->fetchAll();
        $this->assertEquals(1, count($result));

        $result = $instance->fetchAssoc();
        $this->assertEquals('1', $result['id']);
        $this->assertEquals('John', $result['name']);
    }

    public function testQuerySimpleSelectUsingLimitAndOffset() {
        $instance = new Helper('table_');

        $instance->query($instance->select(array('uid', 'id'), 'name')->from('table.users')->limit(1)->offset(1));
        $result = $instance->fetchAssoc();
        $this->assertEquals('2', $result['id']);
        $this->assertEquals('Jack', $result['name']);
    }

    public function testQuerySimpleSelectUsingOrderASC() {
        $instance = new Helper('table_');

        $instance->query($instance->select(array('uid', 'id'), 'name')->from('table.users')->order('id')->limit(1));

        $result = $instance->fetchAll();
        $this->assertEquals(1, count($result));

        $result = $instance->fetchAssoc();
        $this->assertEquals('1', $result['id']);
        $this->assertEquals('John', $result['name']);
    }

    public function testQuerySimpleSelectUsingOrderDESC() {
        $instance = new Helper('table_');

        $instance->query($instance->select(array('uid', 'id'), 'name')->from('table.users')->order('id', Helper::ORDER_DESC)->limit(1));
        $result = $instance->fetchAssoc();
        $this->assertEquals('2', $result['id']);
        $this->assertEquals('Jack', $result['name']);
    }

    public function testQuerySimpleSelectUsingWhere() {
        $instance = new Helper('table_');

        $instance->query($instance->select(array('uid', 'id'), 'name')->from('table.users')->where(Expression::equal('uid', 2)));
        $result = $instance->fetchAssoc();
        $this->assertEquals('2', $result['id']);
        $this->assertEquals('Jack', $result['name']);
    }

    public function testQuerySimpleSelectUsingGroup() {
        $instance = new Helper('table_');

        $instance->query($instance->select(array('uid', 'id'), 'name')->from('table.users')->group('name'));
        $result = $instance->fetchAssoc();
        $this->assertEquals('2', $result['id']);
        $this->assertEquals('Jack', $result['name']);
    }

    public function testQuerySimpleSelectUsingGroupAndHaving() {
        $instance = new Helper('table_');

        $instance->query($instance->select(array('uid', 'id'), 'name')->from('table.users')->group('name')->having(Expression::smallerThan('uid', 2)));
        $result = $instance->fetchAssoc();
        $this->assertEquals('1', $result['id']);
        $this->assertEquals('John', $result['name']);
    }

    public function testQuerySimpleSelectUsingJoin() {
        $instance = new Helper('table_');

        $instance->query($instance->select(array('table.users.name', 'user'), array('table.options.name', 'option'))->from('table.users')
                ->join('table.options')
                ->on(Expression::equal('table.users.uid', 'table.options.for')));

        $result = $instance->fetchAssoc();
        $this->assertEquals('Jack', $result['user']);
        $this->assertEquals('JackOptions', $result['option']);
    }

    public function testQuerySimpleInsert() {
        $instance = new Helper('table_');

        $instance->query($instance->insert('table.users')->rows(array(
            'name'     => 'Lisa',
            'password' => 'LisaPassword'
        )));
        $this->assertEquals(3, $instance->lastInsertId());

        $instance->query($instance->select('uid')->from('table.users'));
        $this->assertEquals(3, count($instance->fetchAll()));
    }

    public function testQuerySimpleInsertMultiRows() {
        $instance = new Helper('table_');

        $instance->query($instance->insert('table.options')->keys('name', 'value')->values(
            array('options1', 'value1'),
            array('options2', 'value2'),
            array('options3', 'value3'),
            array('options4', 'value4')
        ));
        $instance->query($instance->select('name')->from('table.options'));
        $this->assertEquals(6, count($instance->fetchAll()));
    }

    public function testQueryInsertSelect() {
        $instance = new Helper('table_');

        $instance->query($instance->insert('table.options')->keys('name', 'value')->insertSelect($instance->select('value', 'name')->from('table.options')));
        $instance->query($instance->select('name')->from('table.options'));
        $this->assertEquals(12, count($instance->fetchAll()));
    }

    public function testQuerySimpleUpdate() {
        $instance = new Helper('table_');

        $instance->query($instance->update('table.users')->set(array(
            'password' => 'newJackPassword'
        ))->where(Expression::equal('name', 'Jack')));

        $instance->query($instance->select('password')->from('table.users')->where(Expression::equal('name', 'Jack')));
        $this->assertEquals('newJackPassword', $instance->fetchAssoc('password'));
    }

    public function testQueryUpdateUsingWhereAndMore() {
        $instance = new Helper('table_');

        $instance->query($instance->update('table.options')->set(array(
            'for' => '9'
        ))->where(Expression::equal('for', '0'))->order('name')->limit(5));

        $instance->query($instance->select('name')->from('table.options')->order('name')->where(Expression::equal('table.options.for', '9')));
        $this->assertEquals(5, count($instance->fetchAll()));
    }
}
