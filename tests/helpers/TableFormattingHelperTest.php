<?php

namespace eznio\db\tests\helpers;


use eznio\db\helpers\TableFormattingHelper;
use eznio\db\tests\BaseTest;

class TableFormattingHelperTest extends BaseTest
{
    /**
     * @test
     * @dataProvider formatterTestData
     *
     * @param $sourceData
     * @param $sourceHeades
     * @param $expectedOutput
     */
    public function proceedWithTests($sourceData, $sourceHeades, $expectedOutput)
    {
        $this->assertEquals(
            $expectedOutput,
            TableFormattingHelper::format($sourceData, $sourceHeades)
        );
    }

    public function formatterTestData()
    {
        return [
            [   // Test 1
                [
                    ['1', '2', '3'],
                    ['1', '2', '3'],
                ],
                [
                    'column1', 'column2', 'column3'
                ],
                <<<TABLE
+---------+---------+---------+
| column1 | column2 | column3 |
+---------+---------+---------+
| 1       | 2       | 3       |
| 1       | 2       | 3       |
+---------+---------+---------+

TABLE
            ],  // Test 1 ends

            [   // Test 2
                [
                    ['1', '2', '3'],
                ],
                [
                    'column1', 'column2', 'column3'
                ],
                <<<TABLE
+---------+---------+---------+
| column1 | column2 | column3 |
+---------+---------+---------+
| 1       | 2       | 3       |
+---------+---------+---------+

TABLE
            ],  // Test 2 ends

            [   // Test 3
                [
                    ['1'],
                    ['2'],
                    ['3'],
                ],
                [
                    'column1'
                ],
                <<<TABLE
+---------+
| column1 |
+---------+
| 1       |
| 2       |
| 3       |
+---------+

TABLE
            ],  // Test 3 ends

            [   // Test 4
                [
                ],
                [
                ],
                <<<TABLE
+
+

TABLE
            ],  // Test 4 ends

            [   // Test 5
                [
                    ['a' => '1'],
                    ['b' => '2'],
                    ['c' => '3'],
                ],
                [],
                <<<TABLE
+---+---+---+
| a | b | c |
+---+---+---+
| 1 |   |   |
|   | 2 |   |
|   |   | 3 |
+---+---+---+

TABLE
            ],  // Test 5 ends

            [   // Test 6
                [
                    ['a' => '1'],
                    ['b' => '2'],
                    ['c' => '3'],
                ],
                [
                    'a' => 'Column A',
                    'b' => 'Column B',
                    'c' => 'Column C',
                ],
                <<<TABLE
+----------+----------+----------+
| Column A | Column B | Column C |
+----------+----------+----------+
| 1        |          |          |
|          | 2        |          |
|          |          | 3        |
+----------+----------+----------+

TABLE
            ],  // Test 6 ends

            [   // Test 7
                [
                    ['a' => '1'],
                    ['b' => '2'],
                    ['c' => '3'],
                ],
                [
                    'a' => 'Column A',
                    'd' => 'Column D',
                ],
                <<<TABLE
+----------+----------+---+---+
| Column A | Column D |   |   |
+----------+----------+---+---+
| 1        |          |   |   |
|          |          | 2 |   |
|          |          |   | 3 |
+----------+----------+---+---+

TABLE
            ],  // Test 7 ends
        ];
    }
}
