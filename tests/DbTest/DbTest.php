<?php
require_once 'src/Db.php';

/**
 * Db test case.
 */
class DbTest extends PHPUnit_Framework_TestCase {
    
    /**
     *
     * @var Db
     */
    private $db;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp() {
        parent::setUp();
        
        // TODO Auto-generated DbTest::setUp()
        
        $this->db = new Db(/* parameters */);
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown() {
        // TODO Auto-generated DbTest::tearDown()
        $this->db = null;
        
        parent::tearDown();
    }
    
    /**
     * Constructs the test case.
     */
    public function __construct() {
        // TODO Auto-generated constructor
    }
    
    /**
     * Tests Db->__construct()
     */
    public function test__construct() {
        // TODO Auto-generated DbTest->test__construct()
        $this->markTestIncomplete("__construct test not implemented");
        
        $this->db->__construct(/* parameters */);
    }
    
    /**
     * Tests Db::factory()
     */
    public function testFactory() {
        // TODO Auto-generated DbTest::testFactory()
        $this->markTestIncomplete("factory test not implemented");
        
        Db::factory(/* parameters */);
    }
}

