<?php
/**
 * SDB Helper Test Suite
 * 
 * @package SDB
 * @author  ShadowMan
 */

namespace SDB;

use SDB\Helper;
use SDB\Expression;

class HelperTest extends \PHPUnit_Framework_TestCase {
    /**
     * server info
     * 
     * @var array
     */
    protected $_server = array('127.0.0.1', 3306, 'root', '', 'test');

    public function setUp() {
        Helper::disableStrictMode();
        call_user_func_array(array('SDB\\Helper', 'server'), $this->_server);

        $this->_instance = new Helper('table_');
    }

    public function tearDown() {
        unset($this->_instance);
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
        $instance = $this->_instance;
    }

    public function testUnavaliableAdapter() {
        if (!extension_loaded('pdo_mysql')) {
            $instance = new Helper('table_', Helper::ADAPTER_PDO_MYSQL);
        }
    }

    /**
     * @expectedException Exception
     */
    public function testInvalidServer() {
        Helper::cleanServer();
        Helper::server('127.0.0.1', 3306, 'invalidUser', '', 'test');

        $instance = $this->_instance;
        $instance->connect();
    }

    public function testAddServer() {
        Helper::cleanServer();
        call_user_func_array(array('SDB\\Helper', 'server'), $this->_server);
    }

    public function testBuilderReturnType() {
        $instance = $this->_instance;

        $this->assertInstanceOf('SDB\\Query', $instance->builder());
    }

    public function testBuildSelectReturnType() {
        $instance = $this->_instance;

        $this->assertInstanceOf('SDB\\Query', $instance->select('fields'));
    }

    public function testBuildUpdateReturnType() {
        $instance = $this->_instance;

        $this->assertInstanceOf('SDB\\Query', $instance->update('table'));
    }

    public function testBuildInsertReturnType() {
        $instance = $this->_instance;

        $this->assertInstanceOf('SDB\\Query', $instance->insert('table'));
    }

    public function testBuildDeletReturnType() {
        $instance = $this->_instance;

        $this->assertInstanceOf('SDB\\Query', $instance->delete('table'));
    }

    public function testQuery() {
        $instance = $this->_instance;

        # Insert Test Data
        $scripts = file_get_contents('test.sql', true);
        $scripts = explode(';', $scripts);
        foreach ($scripts as $script) {
            $instance->query($script);
        }
    }

    /**
     * @expectedException Exception
     */
    public function testUnknownQuery() {
        $instance = $this->_instance;

        $instance->query($instance->builder());
    }

    /**
     * @expectedException Exception
     */
    public function testServerInfoInvalid() {
        $instance = $this->_instance;

        $instance->serverInfo();
    }

    public function testServerInfo() {
        $instance = $this->_instance;

        $instance->connect();
        $instance->serverInfo();
    }

    public function testFetchAssoc() {
        $instance = $this->_instance;

        $instance->query('SELECT 1, 2');
        $this->assertEquals(array('1' => '1', '2' => '2'), $instance->fetchAssoc());
    }

    public function testFetchAll() {
        $instance = $this->_instance;

        $instance->query('SELECT 1');
        $this->assertEquals(array(array('1' => '1')), $instance->fetchAll());
    }

    public function testQuerySimpleSelect() {
        $instance = $this->_instance;

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

    public function testFetchAssocByArray() {
        $instance = $this->_instance;

        $instance->query($instance->select()->from('table.users'));
        $this->assertEquals(array('uid' => '1', 'name' => 'John'), $instance->fetchAssoc(array('uid', 'name')));
    }

    public function testListOfQuery() {
        $instance = $this->_instance;

        $instance->select()->from('table.users')->query();
        $this->assertEquals(array('uid' => '1', 'name' => 'John', 'password' => 'JohnPassword'), $instance->fetchAssoc());
    }

    /**
     * @expectedException Exception
     */
    public function testInvalidQuery() {
        $instance = $this->_instance;

        $instance->query($instance->select()->from('table.users')->where(Expression::equal('invalidField', 0)));
    }

    public function testQueryFetchRowByKeys() {
        $instance = $this->_instance;

        $instance->query($instance->select()->from('table.users'));
        $this->assertEquals('John', $instance->fetchAssoc('name'));
    }

    public function testAffectedRows() {
        $instance = $this->_instance;

        $instance->query($instance->select()->from('table.options'));
        $this->assertEquals(2, $instance->affectedRows());
    }

    public function testResetInternelPointer() {
        $instance = $this->_instance;

        $instance->query($instance->select()->from('table.users'));
        $this->assertEquals('John', $instance->fetchAssoc('name'));

        $instance->reset();
        $this->assertEquals('John', $instance->fetchAssoc('name'));
    }

    public function testSeekInternelPointer() {
        $instance = $this->_instance;

        $instance->query($instance->select()->from('table.users'));
        $instance->seek(1);
        $this->assertEquals('Jack', $instance->fetchAssoc('name'));
    }

    public function testQuerySimpleSelectUsingFields() {
        $instance = $this->_instance;

        $instance->query($instance->select('table.users.uid', 'name')->from('table.users'));
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
        $instance = $this->_instance;

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
        $instance = $this->_instance;

        $instance->query($instance->select(array('uid', 'id'), 'name')->from('table.users')->limit(1));

        $result = $instance->fetchAll();
        $this->assertEquals(1, count($result));

        $result = $instance->fetchAssoc();
        $this->assertEquals('1', $result['id']);
        $this->assertEquals('John', $result['name']);
    }

    public function testQuerySimpleSelectUsingLimitAndOffset() {
        $instance = $this->_instance;

        $instance->query($instance->select(array('uid', 'id'), 'name')->from('table.users')->limit(1)->offset(1));
        $result = $instance->fetchAssoc();
        $this->assertEquals('2', $result['id']);
        $this->assertEquals('Jack', $result['name']);
    }

    public function testQuerySelectUsingPage() {
        $instance = $this->_instance;

        $instance->query($instance->select(array('uid', 'id'), 'name')->from('table.users')->page(1, 1));
        $this->assertEquals(1, count($instance->fetchAll()));
    }

    public function testQuerySimpleSelectUsingOrderASC() {
        $instance = $this->_instance;

        $instance->query($instance->select(array('uid', 'id'), 'name')->from('table.users')->order('id')->limit(1));

        $result = $instance->fetchAll();
        $this->assertEquals(1, count($result));

        $result = $instance->fetchAssoc();
        $this->assertEquals('1', $result['id']);
        $this->assertEquals('John', $result['name']);
    }

    public function testQuerySimpleSelectUsingOrderDESC() {
        $instance = $this->_instance;

        $instance->query($instance->select(array('uid', 'id'), 'name')->from('table.users')->order('id', Helper::ORDER_DESC)->limit(1));
        $result = $instance->fetchAssoc();
        $this->assertEquals('2', $result['id']);
        $this->assertEquals('Jack', $result['name']);
    }

    /**
     * @expectedException Exception
     */
    public function testQuerySelectUsingInvalidOrder() {
        $instance = $this->_instance;

        $instance->query($instance->select(array('uid', 'id'), 'name')->from('table.users')->order(123));
    }

    /**
     * @expectedException Exception
     */
    public function testQuerySelectUsingInvalidOrderSort() {
        $instance = $this->_instance;
    
        $instance->query($instance->select(array('uid', 'id'), 'name')->from('table.users')->order('name', 'INVALID_SORT'));
    }

    public function testQuerySimpleSelectUsingWhere() {
        $instance = $this->_instance;

        $instance->query($instance->select(array('uid', 'id'), 'name')->from('table.users')->where(Expression::equal('uid', 2, 'si'), Helper::CONJUNCTION_OR));
        $result = $instance->fetchAssoc();
        $this->assertEquals('2', $result['id']);
        $this->assertEquals('Jack', $result['name']);
    }

    /**
     * @expectedException Exception
     */
    public function testQuerySelectUsingInvalidWhereExpression() {
        $instance = $this->_instance;

        $instance->query($instance->select(array('uid', 'id'), 'name')->from('table.users')->where('INVALID EXPRESSION'));
    }

    /**
     * @expectedException Exception
     */
    public function testQuerySelectUsingInvalidWhereConjunction() {
        $instance = $this->_instance;

        $instance->query($instance->select(array('uid', 'id'), 'name')->from('table.users')->where(Expression::equal('uid', 2, 'si'), 'INVALID'));
    }

    public function testQuerySimpleSelectUsingGroup() {
        $instance = $this->_instance;

        $instance->query($instance->select(array('uid', 'id'), 'name')->from('table.users')->group('name'));
        $result = $instance->fetchAssoc();
        $this->assertEquals('2', $result['id']);
        $this->assertEquals('Jack', $result['name']);
    }

    /**
     * @expectedException Exception
     */
    public function testQuerySimpleSelectUsingInvalidGroup() {
        $instance = $this->_instance;

        $instance->query($instance->select(array('uid', 'id'), 'name')->from('table.users')->group(0));
    }

    public function testQuerySimpleSelectUsingGroupAndHaving() {
        $instance = $this->_instance;

        $instance->query($instance->select(array('uid', 'id'), 'name')->from('table.users')->group('name')->having(Expression::smallerThan('uid', 2)));
        $result = $instance->fetchAssoc();
        $this->assertEquals('1', $result['id']);
    }

    public function testQuerySimpleSelectUsingGroupAndHavingUseOr() {
        $instance = $this->_instance;

        $instance->query($instance->select(array('uid', 'id'), 'name')->from('table.users')->group('name')->having(Expression::smallerThan('uid', 2), Helper::CONJUNCTION_OR));
        $result = $instance->fetchAssoc();
        $this->assertEquals('1', $result['id']);
    }

    public function testQuerySimpleSelectUsingJoin() {
        $instance = $this->_instance;

        $instance->query($instance->select(array('table.users.name', 'user'), array('table.options.name', 'option'))->from('table.users')
            ->join('table.options')
            ->on(Expression::equal('table.users.uid', 'table.options.for')));

        $result = $instance->fetchAssoc();
        $this->assertEquals('Jack', $result['user']);
        $this->assertEquals('JackOptions', $result['option']);
    }

    /**
     * @expectedException Exception
     */
    public function testQuerySelectUsingInvalidJoin() {
        $instance = $this->_instance;

        $instance->query($instance->select(array('table.users.name', 'user'), array('table.options.name', 'option'))->from('table.users')
            ->join('table.options', 'INVALID JOIN')
            ->on(Expression::equal('table.users.uid', 'table.options.for')));
    }

    /**
     * @expectedException Exception
     */
    public function testQuerySelectUsingInvalidOnForExpression() {
        $instance = $this->_instance;

        $instance->query($instance->select(array('table.users.name', 'user'), array('table.options.name', 'option'))->from('table.users')
                ->join('table.options')
                ->on('INVALID'));
    }

    /**
     * @expectedException Exception
     */
    public function testQuerySelectUsingInvalidOnForConjunction() {
        $instance = $this->_instance;
    
        $instance->query($instance->select(array('table.users.name', 'user'), array('table.options.name', 'option'))->from('table.users')
                ->join('table.options')
                ->on(Expression::equal('table.users.uid', 'table.options.for'), 'INVALID CONJUNCTION'));
    }

    /**
     * @expectedException Exception
     */
    public function testQuerySelectUsingInvalidOnExpression() {
        $instance = $this->_instance;

        $instance->query($instance->select()->from('table.users')
            ->join('table.options', 'INVALID JOIN')
            ->on('INVALID'));
    }

    /**
     * @expectedException Exception
     */
    public function testQuerySelectUsingInvalidOnConjunction() {
        $instance = $this->_instance;

        $instance->query($instance->select()->from('table.users')
            ->join('table.options', 'INVALID JOIN')
            ->on(Expression::equal('table.users.uid', 'table.options.for'), 'INVALID CONJUNCTIOn'));
    }

    /**
     * @expectedException Exception
     */
    public function testQuerySelectUsingInvalidHavingExpression() {
        $instance = $this->_instance;

        $instance->query($instance->select()->from('table.users')->having('INVALID EXPRESSION'));
    }

    /**
     * @expectedException Exception
     */
    public function testQuerySelectUsingInvalidHavingConjunction() {
        $instance = $this->_instance;

        $instance->query($instance->select()->from('table.users')->having(Expression::equal('table.users.uid', 'table.options.for'), 'INVALID CONJUNCTION'));
    }

    public function testQuerySimpleInsert() {
        $instance = $this->_instance;

        $instance->query($instance->insert('table.users')->rows(array(
            'name'     => 'Lisa',
            'password' => 'LisaPassword'
        )));
        $this->assertEquals(3, $instance->lastInsertId());

        $instance->query($instance->select('uid')->from('table.users'));
        $this->assertEquals(3, count($instance->fetchAll()));
    }

    public function testQuerySimpleInsertMultiRows() {
        $instance = $this->_instance;

        $instance->query($instance->insert('table.options')->keys('name', 'value')->values(
            array('options1', 'value1'),
            array('options2', 'value2'),
            array('options3', 'value3'),
            array('options4', 'value4')
        ));
        $instance->query($instance->select('name')->from('table.options'));
        $this->assertEquals(6, count($instance->fetchAll()));
    }

    /**
     * @expectedException Exception
     */
    public function testQueryInsertUsingInvalidKey() {
        $instance = $this->_instance;

        $instance->query($instance->insert('table.options')->keys(1, 2, 3));
    }

    /**
     * @expectedException Exception
     */
    public function testQuerySimpleInsertEmptyRows() {
        $instance = $this->_instance;

        $instance->query($instance->insert('table.options'));
    }

    public function testQueryInsertUsingRowsAndKeys() {
        $instance = $this->_instance;

        $instance->query($instance->insert('table.options')->rows(array( 'name' => 'options000', 'value' => 'value000' ))->keys('name', 'value')->values(
            array('options111', 'value111')
        ));

        $instance->query($instance->select('name')->from('table.options'));
        $this->assertEquals(8, count($instance->fetchAll()));
    }

    /**
     * @expectedException Exception
     */
    public function testQueryInsertInvalidValues() {
        $instance = $this->_instance;

        $instance->query($instance->insert('table.options')->values(
            array('options111', 'value111')
        ));
    }

    /**
     * @expectedException Exception
     */
    public function testQueryNotMatchKeyValue() {
        $instance = $this->_instance;

        $instance->query($instance->insert('table.options')->keys('name')->values(
            array('options111', 'value111')
        ));
    }

    public function testQueryUsingDefault() {
        $instance = $this->_instance;

        $instance->query($instance->insert('table.options')->keys('name', 'value', 'for')->values(
            array('options222', 'value222', Helper::DATA_DEFAULT)
        ));

        $instance->query($instance->select('name')->from('table.options'));
        $this->assertEquals(9, count($instance->fetchAll()));
    }

    public function testQueryInsertSelect() {
        $instance = $this->_instance;

        $instance->query($instance->insert('table.options')->keys('name', 'value')->insertSelect($instance->select('value', 'name')->from('table.options')));
        $instance->query($instance->select('name')->from('table.options'));
        $this->assertEquals(18, count($instance->fetchAll()));
    }

    /**
     * @expectedException Exception
     */
    public function testQueryInsertInvalidSelect() {
        $instance = $this->_instance;

        $instance->query($instance->insert('table.options')->keys('name', 'value')->insertSelect('INVALID QUERY'));
    }

    public function testQuerySimpleUpdate() {
        $instance = $this->_instance;

        $instance->query($instance->update('table.users')->set(array(
            'password' => 'newJackPassword'
        ))->where(Expression::equal('name', 'Jack')));

        $instance->query($instance->select('password')->from('table.users')->where(Expression::equal('name', 'Jack')));
        $this->assertEquals('newJackPassword', $instance->fetchAssoc('password'));
    }

    public function testQuerySimpleUpdateUsingDEFAULT() {
        $instance = $this->_instance;

        $instance->query($instance->update('table.options')->set(array(
            'for' => Helper::DATA_DEFAULT
        ))->where(Expression::equal('for', '0')));
    }

    /**
     * @expectedException Exception
     */
    public function testQueryUpdateUsingInvalidSetParams() {
        $instance = $this->_instance;

        $instance->query($instance->update('table.users')->set(array(
            'newJackPassword'
        ))->where(Expression::equal('name', 'Jack')));
    }

    public function testQueryUpdateUsingWhereAndMore() {
        $instance = $this->_instance;

        $instance->query($instance->update('table.options')->set(array(
            'for' => '9'
        ))->where(Expression::equal('for', '0'), Helper::CONJUNCTION_OR)->order('name')->limit(5));

        $instance->query($instance->select('name')->from('table.options')->order('name')->where(Expression::equal('table.options.for', '9')));
        $this->assertEquals(5, count($instance->fetchAll()));
    }

    public function testQueryDelete() {
        $instance = $this->_instance;

        $instance->query($instance->delete('table.options')->where(Expression::equal('for', '9')));
        $instance->query($instance->select('name')->from('table.options'));
        $this->assertEquals(13, count($instance->fetchAll()));
    }

    public function testQueryDeleteUsingMoreOptions() {
        $instance = $this->_instance;

        $instance->query($instance->delete('table.options')->order('value')->where(Expression::equal('for', '0'))
                ->where(Expression::equal('for', '0'), Helper::CONJUNCTION_OR)->limit(1));
        $instance->query($instance->select('name')->from('table.options'));
        $this->assertEquals(12, count($instance->fetchAll()));
    }

    public function testQueryMultiTableDelete() {
        $instance = $this->_instance;

        $instance->query($instance->delete(array('table.articles', 'table.options', 'table.users'), 'table.users')
                ->join(array('table.options', 'table.articles'))
                ->on(Expression::equal('table.users.uid', 'table.options.for'))
                ->on(Expression::equal('table.users.uid', 'table.articles.parent'), Helper::CONJUNCTION_OR)
                ->where(Expression::equal('table.users.uid', '1'))
        );

        $this->assertEquals(3, $instance->affectedRows());
    }

    /**
     * @expectedException Exception
     */
    public function testQueryInvalidMultiTableDelete() {
        $instance = $this->_instance;

        $instance->query($instance->delete(array('table.articles', 'table.options', 'table.users')));
    }
}
