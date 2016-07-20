<?php

namespace eznio\db\drivers;


use eznio\db\exceptions\RuntimeException;
use eznio\db\interfaces\Driver;

/**
 * Class Sqlite
 * @package eznio\db\drivers
 */
class Sqlite implements Driver
{
    /** @var \SQLite3 */
    private $sqliteHandler;

    /**
     * @param string $path path to .sqlite3 data file
     */
    public function __construct($path)
    {
        $this->sqliteHandler = new \SQLite3($path);
        $this->sqliteHandler->enableExceptions(true);
    }

    /**
     * Runs SQL query and returns 2D resulting array
     * @param string $sql query to run
     * @param array $args query named arguments
     * @return array
     * @throws RuntimeException
     */
    public function select($sql, array $args = [])
    {
        $sqliteResult = $this->processRequest($sql, $args);
        $result = [];
        while ($row = $sqliteResult->fetchArray(SQLITE3_ASSOC)) {
            if (array_key_exists('ARRAY_KEY', $row)) {
                $key = $row['ARRAY_KEY'];
                unset($row['ARRAY_KEY']);
                $result[$key] = $row;
            } else {
                $result[] = $row;
            }
        }
        return $result;
    }

    /**
     * Runs SQL query and returns nothing
     * @param string $sql query to run
     * @param array $args query named arguments
     * @param array $args
     * @throws RuntimeException
     */
    public function query($sql, array $args = [])
    {
        $this->processRequest($sql, $args);
    }

    /**
     * Runs SQL query and returns single row as resulting array
     * @param string $sql query to run
     * @param array $args query named arguments
     * @return array
     */
    public function getRow($sql, array $args = [])
    {
        $result = $this->select($sql, $args);
        return is_array($result) ? current($result) : [];
    }

    /**
     * Runs SQL query and returns single column resulting array
     * @param string $sql query to run
     * @param array $args query named arguments
     * @return array
     */
    public function getColumn($sql, array $args = [])
    {
        $result = call_user_func_array([$this, 'select'], func_get_args());
        if (empty($result)) {
            return [];
        }
        $keys = array_keys($result);
        $values = array_column($result, current(array_keys(current($result))));
        return array_combine($keys, $values);
    }

    /**
     * Runs SQL query and returns single cell as result
     * @param string $sql query to run
     * @param array $args query named arguments
     * @return string
     */
    public function getCell($sql, array $args = [])
    {
        $result = call_user_func_array([$this, 'select'], func_get_args());
        $row = current($result);
        if (empty($row)) {
            return null;
        }
        return current($row);
    }

    /**
     * Entity load() shortcut, returns single data row by row ID
     * @param string $table table to select from
     * @param int $id row ID
     * @return array
     */
    public function load($table, $id)
    {
        return $this->getRow(
            sprintf(
                'SELECT ' . '* FROM %s WHERE id = :id',
                $table
            ),
            ['id' => $id]
        );
    }

    /**
     * Inserts new row into table and returns it's ID
     * @param string $table table to insert to
     * @param array $data row data
     * @return int
     */
    public function insert($table, $data)
    {
        $sql = sprintf(
            'INSERT ' . 'INTO %s (%s) VALUES (%s)',
            $table,
            implode(',', array_keys($data)),
            "'" . implode('\',\'', array_values($data)) . "'"
        );
        $this->query($sql);
        return $this->sqliteHandler->lastInsertRowID();
    }

    /**
     * Updates existing row in table
     * @param string $table table to work with
     * @param int $id row ID to update
     * @param array $data new data
     */
    public function update($table, $id, $data)
    {
        array_walk($data, function (&$item, $key) {
            $item = sprintf(
                '%s = %s',
                $key,
                null !== $item ? "'" . $item . "'" : 'NULL'
            );
        });
        $sql = sprintf(
            'UPDATE ' . '%s SET %s WHERE id = %d',
            $table,
            implode(', ', $data),
            $id
        );
        $this->query($sql);
    }

    /**
     * Deletes row
     * @param string $table table name
     * @param int $id row id to delete
     */
    public function delete($table, $id)
    {
        $sql = sprintf(
            'DELETE ' . 'FROM %s WHERE id = %d',
            $table,
            (int) $id
        );
        $this->query($sql);
    }

    /**
     * Runs quesy - creates prepared statement, substitutes named placeholders and executes query
     * @param string $sql query to run
     * @param array $args query arguments
     * @return \SQLite3Result
     * @throws RuntimeException
     */
    private function processRequest($sql, array $args = [])
    {
        try {
            $statement = $this->sqliteHandler->prepare($sql);
            foreach ($args as $argId => $arg) {
                $statement->bindValue($argId, $arg);
            }
            return $statement->execute();
        } catch (\Exception $e) {
            $ex = new RuntimeException($e->getMessage());
            $ex->setInnerException($e);
            throw $ex;
        }
    }
}