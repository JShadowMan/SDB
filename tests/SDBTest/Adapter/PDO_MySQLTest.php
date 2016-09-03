<?php

namespace SDB\Adapter;

use SDB\Helper;
use SDB\HelperTest;

class PDO_MySQLTest extends HelperTest {
    public function setUp() {
        Helper::disableStrictMode();
        call_user_func_array(array('SDB\\Helper', 'server'), $this->_server);

        $this->_instance = new Helper('table_', Helper::ADAPTER_PDO_MYSQL);
    }
}
