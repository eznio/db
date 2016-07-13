# ezn.io\db
DB Entity Collections library inspired by Laravel

## Quickstart

```php
// Initializing database-specific driver
$driver = new \eznio\db\drivers\Sqlite('db/test.sqlite3');


// Creating Entity Manager
$em = new \eznio\db\EntityManager($driver);


// Getting query reposityry by table name
$repo = $em->getRepository('test');


// Getting table row with id = 1
$entity =$repo->findOneById(1);


// Updating and saving entity
$entity->field = 'new value';
$entity->save();
```

## Reference

### Driver

### Entity

### Collection

### EntityManager