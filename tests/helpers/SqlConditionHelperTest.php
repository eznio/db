<?php

namespace tests\eznio\db\helpers;


use eznio\db\helpers\SqlConditionHelper;

class SqlConditionHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider conditionTestData
     *
     * @param $sourceCondition
     * @param $expectedSqlString
     */
    public function proceedWithTests($sourceCondition, $expectedSqlString)
    {
        $this->assertEquals(
            $expectedSqlString,
            SqlConditionHelper::build($sourceCondition)
        );
    }

    public function conditionTestData()
    {
        return [
            [   // Test 1
                [],
                '1 = 1'
            ],  // Test 1 end
            [   // Test 2
                ['field' => 'value'],
                'field = "value"'
            ],  // Test 2 end
            [   // Test 3
                ['or' => [
                    ['field1' => 'value1'],
                    ['field1' => 'value2']
                ]],
                '(field1 = "value1" or field1 = "value2")'
            ],  // Test 3 end
            [   // Test 4
                ['and' => [
                    ['or' => [
                        ['field1' => 'value1'],
                        ['field1' => 'value2']
                    ]],
                    ['or' => [
                        ['field2' => 'value3'],
                        ['field2' => 'value4']
                    ]],
                ]],
                '((field1 = "value1" or field1 = "value2") and (field2 = "value3" or field2 = "value4"))'
            ],  // Test 4 end
        ];
    }

    /**
     * @test
     * @expectedException \LogicException
     * @expectedExceptionMessage SqlConditionHelper expects array as input, got string
     */
    public function shouldThrowLogicExceptionOnInvalidInput()
    {
        SqlConditionHelper::build('sql');
    }

    /**
     * @test
     * @expectedException \LogicException
     * @expectedExceptionMessage SqlConditionHelper logic delimiter expected to be "or" or "and", got xor
     */
    public function shouldThrowLogicExceptionOnInvalidDelimiter()
    {
        SqlConditionHelper::build(['xor' => ['foo' => 'bar', 'omg' => 'wtf']]);
    }
}
