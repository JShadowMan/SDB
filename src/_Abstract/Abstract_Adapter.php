<?php
namespace Db\_Abstract;

/**
 *
 * @author ShadowMan
 */
interface Abstract_Adapter {
    public static function isAvaliable();

    public function serverInfo();

    public function connect();

    public function lastInsertId();

    public static function escapeKey($key);

    public static function escapeValue($value);

    public static function parseSelect($preBuilder);

    public static function parseUpdate($preBuilder);

    public static function parseInsert($preBuilder);

    public static function parseDelete($preBuilder);

    public static function parseChange($preBuilder);

    public function query($query);

    public function fetch($instace, $result);
}

?>