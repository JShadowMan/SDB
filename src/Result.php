<?php
/**
 *
 * @author ShadowMan
 */
namespace SDB;

class Result {
    /**
     * result pointer to an arbitrary row in the result
     * 
     * @var int
     */
    private $_currentRow = 0;

    /**
     * total of rows
     * 
     * @var int
     */
    private $_rowsCount = 0;

    /**
     * total of affected rows
     * 
     * @var int
     */
    private $_affectedRows = 0;

    private $_result = null;

    /**
     * Result Class constructor
     * 
     * @param array $result
     */
    public function __construct(array $result, $affectedRows) {
        $this->_currentRow = 0;
        $this->_affectedRows = is_int($affectedRows) ? $affectedRows : -1;
        $this->_rowsCount = count($result);
        $this->_result = $result;
    }

    /**
     * Fetches all result rows as an associative array
     */
    public function fetchAll() {
        return $this->_result;
    }

    /**
     * Get a result row as an associative array
     */
    public function fetch() {
        $keys = func_get_args();

        // fetch all fields
        if (empty($key)) {
            return $this->_result[$this->_currentRow++];
        }

        // fetch some field
        $result = array();
        foreach ($keys as $key) {
            $result[$key] = isset($this->_result[$this->_currentRow][$key]) ? $this->_result[$this->_currentRow][$key] : null;

            $this->_currentRow += 1;
        }
        return $result;
    }

    /**
     * Adjusts the result pointer to an arbitrary row in the result
     * 
     * @param int $offset
     */
    public function seek($offset) {
        $this->_currentRow = ($offset >= 0 && $offset <= $this->_rowsCount) ? $offset : $this->_currentRow;
    }

    /**
     * Reset the result pointer
     */
    public function reset() {
        $this->_currentRow = 0;
    }

    /**
     * Magic method
     * 
     * @param unknown $name
     */
    public function __get($name) {
        $name = '_' + $name;

        return $this->{$name};
    }
}

?>