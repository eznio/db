Some snippets to start with. More info in [Reference](reference.md).

## Driver

If you need to work with Driver directly - have a look at `eznio\db\driver\Driver` interface in [Reference](reference.md) section.

```php
// Initializing database-specific driver
$driver = new \eznio\db\drivers\Sqlite('db/test.sqlite3');

// Getting some data from table
$result = $driver->select('SELECT * FROM users WHERE name = :name', ['name' => 'John']);

/*  $result = [
 *      ['id' => 1, 'name' => 'John', 'surname' => 'Smith'],
 *      ['id' => 2, 'name' => 'John', 'surname' => 'Doe']
 */ ];
```

## Entity

Entity is an ActiveRecord representing single table row.

Good idea is to create your own entities by extending base one to provide IDE-level code completion and (if needed) - some extra logic.

```php
/**
 * Class UsersEntity
 *
 * @method string getName()
 * @method UsersEntity setName($name)
 * @method string getEmail()
 * @method UsersEntity setEmail($email)
 */
class UsersEntity extends \eznio\db\Entity
{
    /**
     * UsersEntity constructor.
     * @param Driver $driver
     */
    public function __construct(Driver $driver)
    {
        parent::__construct($driver, 'members');
    }
}

// Not quite a good idea, use EntityManager::createEntity() instead, but goes for an example...
$driver = new \eznio\db\drivers\Sqlite('db/test.sqlite3');
$entity = new UsersEntity($driver);
$entity
    ->setName('Mike')
    ->setEmail('mike@example.com')
    ->save();

echo "ID: " . $entity->getId() . "\n" . $entity->asTable();
```

```
ID: 1
+----+------+------------------+
| id | name |      email       |
+----+------+------------------+
|  1 | Mike | mike@example.com |
+----+------+------------------+
```

## Repository

Repositories are lists of queries which can be made on their underlying table:

```php
/**
 * Class UsersRepository
 */
class UsersRepository extends \eznio\db\Repository
{
    /**
     * UsersRepository constructor.
     * @param Driver $driver
     */
    public function __construct(Driver $driver)
    {
        parent::__construct($driver, 'users');
    }
}

$driver = new \eznio\db\drivers\Sqlite('db/test.sqlite3');
$usersRepo = new UsersRepository($driver);

/** @var \eznio\db\Entity $singleUser */
$singleUser = $usersRepo->findOneById(2);
```

Base Repository class has some built-in methods, derived classes are to be further extended to suite your needs.

## Collection

Set of entities. Some group operations can be done on them.

```php
$driver = new \eznio\db\drivers\Sqlite('db/test.sqlite3');
$usersRepo = new UsersRepository($driver);

/** @var \eznio\db\Collection $users */
$users = $usersRepo->getAll();

// Collections can be iterated as arrays:
foreach ($user in $users) {
    /** @var \eznio\db\Entity $user */
    $user
        ->setAge(random(18, 90))
        ->save();
}
```

## EntityManager

This one is mostly factory/locator for entities and repositories:

```php
$driver = new \eznio\db\drivers\Sqlite('db/test.sqlite3');
$em = new \eznio\db\EntityManager($driver);

$repo = $em->getRepository('users');

$entity = $em->createEntity('users');

```