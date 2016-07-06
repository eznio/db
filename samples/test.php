<?php

require '../vendor/autoload.php';

$driver = new \eznio\db\drivers\Sqlite('db/test.sqlite3');

$em = new \eznio\db\EntityManager($driver);
//echo $em->createEntity('test')
//    ->load(1)
//    ->toTable();

$repo = $em->getRepository('test');
//echo $repo->getAll()->toTable(['id', 'key']);
echo $repo->getAll()->toTable();