<?php

namespace eznio\db\interfaces;


interface Driver
{
    public function select($sql, array $args = []);
    public function query($sql, array $args = []);
    public function getRow($sql, array $args = []);
    public function getColumn($sql, array $args = []);
    public function getCell($sql, array $args = []);
    public function load($table, $id);
    public function insert($table, $data);
    public function update($table, $id, $data);
    public function delete($table, $id);
}
