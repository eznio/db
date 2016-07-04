<?php

require '../vendor/autoload.php';

$driver = new \eznio\db\drivers\Sqlite('db/test.sqlite3');

$em = new \eznio\db\EntityManager($driver);
//$testEntity = $em->createEntity('test');
//$testEntity->load(1);
//var_dump($testEntity->asArray());

$repo = $em->getRepository('test');
echo $repo->getAll()->toTable(['id', 'key']);
//echo $repo->getAll()->toTable();