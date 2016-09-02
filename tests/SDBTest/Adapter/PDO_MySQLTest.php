<?php

namespace SDB\Adapter;

use SDB\Helper;
use SDB\HelperTest;

class PDO_MySQLTest extends HelperTest {
    public function setUp() {
        Helper::disableStrictMode();
        Helper::server('127.0.0.1', 3306, 'root', '', 'test');

        $this->_instance = new Helper('table_', Helper::ADAPTER_PDO_MYSQL);
    }
}
