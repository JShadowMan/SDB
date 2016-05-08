<?php
/**
 * Db test case.
 */
class DbTest extends PHPUnit_Framework_TestCase {
    public static function db($adapter = Db::DB_ADAPTER_MYSQL, $prefix = null) {
        return \Db::factory($adapter, $prefix);
    }

    public function testServer() {
        $this->assertEquals('SERVER', Db::server());
    }

    public function testDefaultClass() {
        $db = self::db();

        $this->assertEquals(Db::DB_ADAPTER_MYSQL, $db->getAdapter());
        $this->assertEquals(null, $db->getPrefix());
    }
    public function testCustomClass() {
        $db = self::db(Db::DB_ADAPTER_ORACLE, 'table_');

        $this->assertEquals(Db::DB_ADAPTER_ORACLE, $db->getAdapter());
        $this->assertEquals('table_', $db->getPrefix());
    }
}

