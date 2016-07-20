<?php

namespace eznio\db\tests\helpers;


use eznio\db\helpers\NameTranslateHelper;
use eznio\db\tests\BaseTest;

class NameTranslateHelperTest extends BaseTest
{
    /**
     * @test
     * @dataProvider fieldToFunctionTestData
     *
     * @param $sourceField string
     * @param $expectedFunction string
     */
    public function proceedWithFieldToFunctionTests($sourceField, $prefix, $expectedFunction)
    {
        $this->assertEquals(
            $expectedFunction,
            NameTranslateHelper::fieldToFunction($sourceField, $prefix)
        );
    }

    public function fieldToFunctionTestData()
    {
        return [
            [
                '',
                '',
                ''
            ],
            [
                'field',
                '',
                'Field'
            ],
            [
                'field',
                'set',
                'setField'
            ],
            [
                'long_field',
                'set',
                'setLongField'
            ],
            [
                '_field',
                'set',
                'setField'
            ],
            [
                'field_',
                'set',
                'setField_'
            ],
        ];
    }

    /**
     * @test
     * @dataProvider functionToFieldTestData
     *
     * @param $sourceFunction string
     * @param $expectedField string
     */
    public function proceedWithFunctionToFieldTests($sourceFunction,$expectedField)
    {
        $this->assertEquals(
            $expectedField,
            NameTranslateHelper::functionToField($sourceFunction)
        );
    }

    public function functionToFieldTestData()
    {
        return [
            [
                '',
                ''
            ],
            [   // See class' header!
                'somefield',
                'field'
            ],
            [
                'getField',
                'field'
            ],
            [
                'setMyLongField',
                'my_long_field'
            ]
        ];
    }
}
