<?php
namespace Db;

use Db\_Abstract\Abstract_Adapter;
use Db\Adapter\Adapter_MySQL;
/**
 * @author ShadowMan
 */

class Helper {
    /**
     * store server infomation
     * consisted of SERVER_ADDR, SERVER_PORT, USERNAME, PASSWORD, DATABASE, TABLE, TABLE_PREFIX
     * 
     * @var array
     */
    private static $_server = array();

    /**
     * store server adapter infomation
     * 
     * @var Abstract_Adapter
     */
    private $_adapter = null;

    /**
     * store table prefix 
     * 
     * @example 'table.<TABLE_NAME>'
     * @var string
     */
    private $_prefix  = null;

    /**
     * 
     * @param string $adapter
     * @param string $prefix
     */
    public function __construct($adapter, $prefix = null) {
        if (!in_array($adapter, array(self::DB_ADAPTER_MYSQL, self::DB_ADAPTER_ORACLE, self::DB_ADAPTER_SQL_SERVER, self::DB_ADAPTER_SQLITE))) {
            throw new \Exception('Adapter Not Defined.', 1);
        }
        if ($prefix != null && !is_string($prefix)) {
            throw new \Exception('table prefix is not string', 2);
        }

        switch ($adapter) {
            case self::DB_ADAPTER_MYSQL: $this->_adapter = new Adapter\Adapter_MySQL(); break;
            case self::DB_ADAPTER_ORACLE:
            case self::DB_ADAPTER_SQL_SERVER:
            case self::DB_ADAPTER_SQLITE:
            default:
                break;
        }

        $this->_prefix  = $prefix;
    }

    public static function factory($adapter, $prefix = null) {
        return new Helper($adapter, $prefix);
    }

    public static function server() {
        return 'SERVER';
    }

    /**
     * 
     * @return \Db\_Abstract\Abstract_Adapter
     */
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