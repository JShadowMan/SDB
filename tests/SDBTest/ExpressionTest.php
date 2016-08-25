<?php
/**
 *
 * @author ShadowMan
 */

use SDB\Expression;

class ExpressionTest extends \PHPUnit_Framework_TestCase {

    public function testEqual() {
        $expression = Expression::equal('apple', '1');

        $this->assertEquals('apple', $expression->lval());
        $this->assertEquals('1', $expression->rval());
        $this->assertEquals('=', $expression->operator());
    }

    public function testNotEqual() {
        $expression = Expression::notEqual('apple', '1');

        $this->assertEquals('apple', $expression->lval());
        $this->assertEquals('1', $expression->rval());
        $this->assertEquals('!=', $expression->operator());
    }

    public function testBiggerThan() {
        $expression = Expression::biggerThan('apple', '1');

        $this->assertEquals('apple', $expression->lval());
        $this->assertEquals('1', $expression->rval());
        $this->assertEquals('>', $expression->operator());
    }

    public function testBiggerThanOrEqualTo() {
        $expression = Expression::biggerThanOrEqualTo('apple', '1');

        $this->assertEquals('apple', $expression->lval());
        $this->assertEquals('1', $expression->rval());
        $this->assertEquals('>=', $expression->operator());
    }

    public function testSmallerThan() {
        $expression = Expression::smallerThan('apple', '1');

        $this->assertEquals('apple', $expression->lval());
        $this->assertEquals('1', $expression->rval());
        $this->assertEquals('<', $expression->operator());
    }

    public function testSmallerThanOrEqualTo() {
        $expression = Expression::smallerThanOrEqualTo('apple', '1');

        $this->assertEquals('apple', $expression->lval());
        $this->assertEquals('1', $expression->rval());
        $this->assertEquals('<=', $expression->operator());
    }

    public function testReturnValueType() {
        $expression = Expression::smallerThanOrEqualTo('apple', '1');

        $this->assertInstanceOf('SDB\\Expression', $expression);
    }

    public function testGettingData() {
        $expression = Expression::equal('apple', '1');

        $this->assertEquals(array('lval' => 'apple', 'operator' => '=', 'rval' => '1'), $expression->expression());
    }

    public function testValueTypeCheckForInt() {
        $expression = Expression::equal('apple', 1, 'si');

        $this->assertEquals(1, $expression->rval());
        $this->assertInternalType('int', $expression->rval());
    }

    public function testValueTypeCheckForDouble() {
        $expression = Expression::equal('apple', 1.23456789, 'sd');

        $this->assertEquals(1.23456789, $expression->rval());
        $this->assertInternalType('double', $expression->rval());
    }

    public function testValueTypeCheckForFloat() {
        $expression = Expression::equal('apple', 1.23456789, 'sf');

        $this->assertEquals(1.23456789, $expression->rval());
        $this->assertInternalType('float', $expression->rval());
    }

    public function testValueTypeCheckForBoolean() {
        $expression = Expression::equal('apple', true, 'sb');

        $this->assertEquals(true, $expression->rval());
        $this->assertInternalType('bool', $expression->rval());
    }

    public function testValueTypeCheckForMixed() {
        $expression = Expression::equal(12345, true, 'mm');

        $this->assertEquals(12345, $expression->lval());
        $this->assertInternalType('int', $expression->lval());

        $this->assertEquals(true, $expression->rval());
        $this->assertInternalType('bool', $expression->rval());
    }

    /**
     * @expectedException Exception
     */
    public function testValueTypeCheckForUndefinedType() {
        $expression = Expression::equal(12345, true, 'xx');
    }

    public function testValueTypeCheckForAutoConversions() {
        $expression = Expression::equal('apple', '12345', 'si');

        $this->assertEquals(12345, $expression->rval());
        $this->assertInternalType('int', $expression->rval());
    }

    /**
     * @expectedException Exception
     */
    public function testValueTypeInvalidForMoreOptions() {
        $expression = Expression::equal('apple', '12345', 'sii');
    }

    /**
     * @expectedException Exception
     */
    public function testValueTypeInvalidForInvalidArgs() {
        $expression = Expression::equal('apple', '12345', false);
    }

    public function testSymbol() {
        $symbols = Expression::symbol();

        $this->assertEquals(array('e' => '=', 'ne' => '!=', 'gt' => '>', 'gte' => '>=', 'lt' => '<', 'lte' => '<='), $symbols);
    }

    public function testChangeOperator() {
        $symbols = Expression::symbol('==');

        $this->assertEquals(array('e' => '==', 'ne' => '!=', 'gt' => '>', 'gte' => '>=', 'lt' => '<', 'lte' => '<='), $symbols);
        $symbols = Expression::symbol('=');
    }

    public function testGettingDataUsingCallback() {
        $expression = Expression::equal(3, 4, 'ii');
        $data = $expression->expression(function($v) { return $v * $v; }, function($v) { return $v + $v; });

        $this->assertEquals(9, $data['lval']);
        $this->assertEquals(8, $data['rval']);
    }

    public function testGettingLeftValueUsingCallback() {
        $expression = Expression::equal(3, 4, 'ii');

        $this->assertEquals(9, $expression->lval(function($v) { return $v * $v; }));
    }

    public function testGettingRightValueUsingCallback() {
        $expression = Expression::equal(3, 4, 'ii');

        $this->assertEquals(8, $expression->rval(function($v) { return $v + $v; }));
    }
}
