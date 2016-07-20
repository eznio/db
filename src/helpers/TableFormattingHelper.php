<?php

namespace eznio\db\helpers;


use eznio\ar\Ar;

/**
 * Formats 2D associative array to ascii-graphics table
 * @package eznio\db\helpers
 */
class TableFormattingHelper
{
    /**
     * Returns string with generated table
     * @param array $data 2D array with table data
     * @param array $headers array with table column headers
     * @param bool $tryToGuessHeaders if we shold take header names from array column keys
     * @return string
     */
    public static function format(array $data, array $headers = [], $tryToGuessHeaders = true)
    {
        $columnNames = self::getColumnNames($data, $headers);
        $maxLengths = self::getMaxLengths($data, $headers, $columnNames);

        if (true === $tryToGuessHeaders && 0 === count($headers)) {
            $headers = array_combine($columnNames, $columnNames);
        }

        $response = [];
        if (count($headers) > 0) {
            $response[] = self::getSeparatorRow($maxLengths);
            $response[] = self::getHeaderRow($headers, $maxLengths);
        }
        $response[] = self::getSeparatorRow($maxLengths);
        foreach ($data as $row) {
            $response[] = self::getRow($row, $maxLengths);
        }
        $response[] = self::getSeparatorRow($maxLengths);


        return implode("\n", $response) . "\n";
    }

    /**
     * Gathers list of column names
     * @param array $data
     * @param array $headers
     * @return array
     */
    private static function getColumnNames($data, $headers)
    {
        $columns = array_keys($headers);
        foreach ($data as $row) {
            $columns = array_unique(array_merge($columns, array_keys($row)));
        }
        return $columns;
    }

    /**
     * Returns max string length per each column
     * @param array $data 2D array with table data
     * @param array $headers array with table column headers
     * @param array columns column key names
     * @return array
     */
    private static function getMaxLengths($data, $headers, $columns)
    {
        $maxLengths = [];
        foreach ($data as $rowId => $row) {
            if (!is_array($row)) {
                continue;
            }

            foreach ($columns as $columnId) {
                $maxLengths[$columnId] = max(
                    Ar::get($maxLengths, $columnId),
                    strlen(Ar::get($row, $columnId)),
                    strlen(Ar::get($headers, $columnId))
                );
            }
//
//            foreach ($row as $columnId => $item) {
//                $item = preg_replace('/\<[^\|]+\|([^\>]+)\>/', '$1', $item);
//                if (!array_key_exists($columnId, $maxLengths)) {
//                    $maxLengths[$columnId] = strlen($item);
//                } elseif (strlen($item) > $maxLengths[$columnId]) {
//                    $maxLengths[$columnId] = strlen($item);
//                }
//            }
        }

        if (0 === count($headers)) {
            return $maxLengths;
        }

        if (count($headers) !== count(array_keys(current($data)))) {
            return $maxLengths;
        }

        $headers = array_combine(array_keys(current($data)), $headers);
        foreach ($headers as $columnId => $item) {
            if (strlen($item) > Ar::get($maxLengths, $columnId)) {
                $maxLengths[$columnId] = strlen($item);
            }
        }

        return $maxLengths;
    }

    /**
     * Generates row for table top, bottom and headers/data separator
     * @param array $maxLengths max column text lengths
     * @return string
     */
    private static function getSeparatorRow($maxLengths)
    {
        $response[] = '';
        foreach ($maxLengths as $length) {
            $response[]= str_pad('', $length + 2, '-');
        }
        $response[] = '';
        return implode('+', $response);
    }

    /**
     * Returns table data row, columns are right-padded
     * @param array $row row data
     * @param array $maxLengths max column text lengths
     * @return string
     */
    private static function getRow($row, $maxLengths)
    {
        $response = '|';
        foreach ($maxLengths as $itemId => $item) {
            $response .= ' ' . str_pad(Ar::get($row, $itemId), $item, ' ', STR_PAD_RIGHT) . ' |';
        }
        return $response;
    }

    /**
     * Returns table header row, columns are centered
     * @param array $row table headers
     * @param array $maxLengths max column text lengths
     * @return string
     */
    private static function getHeaderRow($row, $maxLengths)
    {
        $response = '|';
        foreach ($maxLengths as $itemId => $item) {
            $response .= ' ' . str_pad(Ar::get($row, $itemId), $item, ' ', STR_PAD_BOTH) . ' |';
        }
        return $response;
    }
}