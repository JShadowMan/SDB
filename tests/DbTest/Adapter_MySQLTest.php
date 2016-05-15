<?php
use Db\Helper;
/**
 *
 * @author ShadowMan
 */
class Adapter_MySQLTest extends \PHPUnit_Framework_TestCase {
    public function testAdapterMySQLisAvaliable() {
        $helper = \Db\Helper::factory(\Db\Helper::DB_ADAPTER_MYSQL, 'table_');
        $adapter = $helper->getAdapter();

        $this->assertEquals(extension_loaded('mysqli') ? true : false, $adapter->isAvaliable());
    }
}

?>