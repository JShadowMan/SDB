<?php

/**
 *
 * @author ShadowMan
 */
class Db {
    private static $_server = array();

    private $_adapter = null;

    private $_prefix  = null;

    public function __construct($adapter, $prefix = null) {
        $this->_adapter = $adapter;
        $this->_prefix  = $prefix;
    }

    public static function factory($adapter, $prefix = null) {
        return new Db();
    }

    public static function server() {
        return 'SERVER';
    }

    public function getAdapter() {
        return $this->_adapter;
    }

    public function getPrefix() {
        return $this->_prefix;
    }

    # Database: MySQL
    const DB_ADAPTER_MYSQL      = 'MySQL';

    # Database: SQL Server
    const DB_ADAPTER_SQL_SERVER = 'SQL_SERVER';

    # Database: Oracle
    const DB_ADAPTER_ORACLE     = 'ORACLE';

    # Database: SQLite
    const DB_ADAPTER_SQLITE     = 'SQLITE';

    # CRUD Operator: Create
    const DB_OPERATOR_CREATE    = 'CREATE';

    # CRUD Operator: Read
    const DB_OPERATOR_READ      = 'READ';

    # CRUD Operator: Update
    const DB_OPERATOR_UPDATE    = 'UPDATE';

    # CRUD Operator: Delete
    const DB_OPERATOR_DELETE    = 'DELETE';
}

?>