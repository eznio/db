<?php

namespace eznio\db\helpers;

/**
 * Class NameTranslateHelper
 * Translates snake case ("my_long_field_name") to cames case ("MyLongFieldName") and back
 * @package eznio\db\helpers
 */
class NameTranslateHelper
{
    /**
     * Camel case to snake case
     * @param string $function function name in camel case
     * @return string
     */
    public static function functionToField($function)
    {
        return substr(preg_replace_callback('/[A-Z]./', function($item) {
            return '_' . strtolower(current($item));
        }, $function), 4);
    }

    /**
     * Snake case to camel case with possible prefix addition ("my_field" -> "getMyField")
     * @param string $field field name in snake case
     * @param string $prefix optional prefix to add
     * @return string
     */
    public static function fieldToFunction($field, $prefix = '')
    {
        return $prefix . ucfirst(preg_replace_callback('/_[a-z]{1}/', function($item) {
            return strtoupper(substr(current($item), 1));
        }, $field));
    }
}