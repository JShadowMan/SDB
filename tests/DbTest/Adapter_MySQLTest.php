<?php
use Db\Helper;

/**
 *
 * @author ShadowMan
 */
class Adapter_MySQLTest extends \PHPUnit_Framework_TestCase {
    public function testAdapterMySQLisAvaliable() {
        Helper::server('localhost', '3306', 'root', 'root', 'test');

        $helper = Helper::factory(Helper::DB_ADAPTER_MYSQL, 'table_');
        $adapter = $helper->getAdapter();

        $this->assertEquals(extension_loaded('mysqli') ? true : false, $adapter->isAvaliable());
    }

    /**
     * @expectedException Exception
     */
    public function testAdapterSQLiteisAvaliable() {
        $helper = Helper::factory(Helper::DB_ADAPTER_SQLITE, 'table_');
        $adapter = $helper->getAdapter();

        $this->assertEquals(extension_loaded('sqlite') ? true : false, $adapter->isAvaliable());
    }
}

?>