<?php

require '../vendor/autoload.php';

//$driver = new \eznio\db\drivers\Sqlite('db/test.sqlite3');
//
//$em = new \eznio\db\EntityManager($driver);
////echo $em->createEntity('test')
////    ->load(1)
////    ->toTable();
//
//$repo = $em->getRepository('test');
////echo $repo->getAll()->toTable(['id', 'key']);
//echo $repo->getAll()->toTable();

//$driver = new \eznio\db\drivers\Mysql('mysql:dbname=test;host=192.168.33.17', 'test', 'test');
//$res = $driver->load('test', 2);
//var_dump($res);

$data = [
    ['a' => 332423423, 'c' => 3],
    ['b' => 1, 'a' => 2],
    ['c' => 444, 'b' => 5],
];


$data = [
    ['1', '2', '3'],
    ['1', '2', '3'],
];
$headers = ['column1', 'column2', 'column3'];

echo \eznio\db\helpers\TableFormattingHelper::format($data, $headers);