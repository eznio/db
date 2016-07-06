<?php

namespace eznio\db\helpers;

/**
 * Class SqlConditionHelper
 * Recursively generates nested SQL WHERE conditions from array
 * 1. Simple condition:
 *    ['field' => 'value'] is converted to 'field = "value"'
 * 2. Condition with logical operation
 *    ['or' => [
 *        ['field1' => 'value1'],
 *        ['field2' => 'value2']
 *    ] is converted to '(field1 = "value1" OR field2 = "value2")
 * 3. Locical conditions can be nested:
 *    ['and' => [
 *         ['or' =>
 *             ['field1' => 'value1'],
 *             ['field1' => 'value2']
 *         ],
 *         ['or' =>
 *             ['field2' => 'value3'],
 *             ['field2' => 'value4']
 *         ],
 *    ] is converted to '((field1 = "value1") OR (field1 = "value2")) AND ((field2 = "value3") OR (field2 = "value4"))
 *
 * @package eznio\db\helpers
 */
class SqlConditionHelper
{
    /**
     * Builds condition string from array
     * @param array $conditions condition(-s) in format described above
     * @return string
     */
    public static function build($conditions)
    {
        if (!is_array($conditions)) {
            throw new \LogicException(sprintf('SqlConditionHelper expects array as input, got %s', gettype($conditions)));
        }
        if (0 === count($conditions)) {
            return '1 = 1';
        }
        list($op, $condition) = each($conditions);
        if (in_array(strtolower($op), ['and', 'or'])) {
            return self::buildCompositeCondition($op, $condition);
        } elseif (is_array($condition)) {
            throw new \LogicException(
                sprintf('SqlConditionHelper logic delimiter expected to be "or" or "and", got %s', $op)
            );
        }
        return self::buildSimpleCondition($conditions);
    }

    /**
     * Generates simple condition string (field = "value")
     * @param array $condition
     * @return string
     */
    private static function buildSimpleCondition(array $condition)
    {
        list($key, $value) = each($condition);
        return self::escapePair($key, $value);
    }

    /**
     * Recursively generates logical-imploded condition string
     * @param string $op "or" or "and" operation name
     * @param array $conditions
     * @return string
     */
    private static function buildCompositeCondition($op, array $conditions)
    {
        $result = [];
        foreach ($conditions as $condition) {
            $result[] = self::build($condition);
        }
        return '(' . implode(' ' . $op . ' ', $result) . ')';
    }

    /**
     * Proceeds with SQL escaping
     * @param string $key field name
     * @param string $value field value
     * @return string
     */
    private static function escapePair($key, $value)
    {
        if (is_int($value)) {
            return sprintf('%s = %d', $key, $value);
        }
        if (null === $value) {
            return sprintf('%s = NULL', $key);
        }
        return sprintf('%s = "%s"', $key, $value);
    }
}