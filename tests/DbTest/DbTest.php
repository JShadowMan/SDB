<?php
/**
 * Db test case.
 */
class DbTest extends PHPUnit_Framework_TestCase {
    public static function db() {
        return new \Db();
    }

    public function testInit() {
        $db = self::db();

        $this->assertEquals('initialized', $db->init());
    }
}

