<?php

namespace eznio\db\drivers;


use eznio\db\interfaces\Driver;

/**
 * Class Mysql
 * @package eznio\db\drivers\
 */
class Mysql implements Driver
{
    /** @var string|null */
    private $dsn = null;

    /** @var string|null */
    private $login = null;

    /** @var string|null */
    private $password = null;

    /** @var \PDO|null */
    private $dbHandler = null;

    /**
     * Mysql constructor.
     * @param string $dsn FQDSN
     * @param string $login DB login
     * @param string $password DB password
     */
    public function __construct($dsn, $login, $password)
    {
        $this->dsn = $dsn;
        $this->login = $login;
        $this->password = $password;
    }

    /**
     * Processes SQL query and returns 2D resulting array
     * @param string $sql
     * @param array $args
     * @return array
     */
    public function select($sql, array $args = [])
    {
        $sqlResult = $this->processRequest($sql, $args);
        $result = [];
        while ($row = $sqlResult->fetch(\PDO::FETCH_ASSOC)) {
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
     * Processes SQL query and returns nothing
     * @param string $sql
     * @param array $args
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
        return $this->dbHandler->lastInsertId();
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
     * Checks if DB connection has been established
     * @return bool
     */
    public function isConnected()
    {
        return null !== $this->dbHandler;
    }

    /**
     * Processes PDO DB request and returns resulting PDOStatement
     * @param string $sql
     * @param array $placeholders
     * @return \PDOStatement
     */
    private function processRequest($sql, array $placeholders = [])
    {
        if (!$this->isConnected()) {
            $this->dbHandler = new \PDO($this->dsn, $this->login, $this->password);
        }

        $statement = $this->dbHandler->prepare($sql);
        foreach ($placeholders as $placeholderId => $placeholderValue) {
            $statement->bindParam($placeholderId, $placeholderValue);
        }
        $statement->execute();
        return $statement;
    }
}