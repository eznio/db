<?php

require '../vendor/autoload.php';

$driver = new \eznio\db\drivers\Sqlite('db/test.sqlite3');

$em = new \eznio\db\EntityManager($driver);
//echo $em->createEntity('test')
//    ->load(1)
//    ->toTable();

$repo = $em->getRepository('test');
echo $repo->getAll()->toTable(['id' => 'id1', 'key' => 'key123']);
//echo $repo->getAll()->toTable();

//$driver = new \eznio\db\drivers\Mysql('mysql:dbname=test;host=192.168.33.17', 'test', 'test');
//$res = $driver->load('test', 2);
//var_dump($res);
