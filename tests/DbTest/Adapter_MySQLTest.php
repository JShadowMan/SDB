<?php
/**
 *
 * @author ShadowMan
 */
class Adapter_MySQLTest extends \PHPUnit_Framework_TestCase {
    public static function helper($adapter = \Db\Helper::DB_ADAPTER_MYSQL, $prefix = null) {
        return new \Db\Helper($adapter, $prefix);
    }

    public function testAdapterMySQLisAvaliable() {
        $helper = self::helper();
        $adapter = $helper->getAdapter();

        $this->assertEquals(true, $adapter->isAvaliable());
    }
}

?>