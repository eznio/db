# Driver

Driver is DB-specific request handling class. Implements the `\eznio\db\drivers\Driver` interface.

#### query()
```php
public function query($sql, array $args = []);
```
Runs SQL query and doesn't return anything. Useful for system queries like "SET NAMES" or similar.

```php
$driver->query("SET NAMES :encoding:", ['encoding' => 'UTF-8'];
```

#### select()
```php
public function select($sql, array $args = []);
```

Runs SQL query and returns produced result as array.

```php
$result => $driver->select("SELECT * FROM users WHERE name = ?", ['John']);

/*  $result = [
 *      ['id' => 1, 'name' => 'John', 'surname' => 'Smith'],
 *      ['id' => 2, 'name' => 'John', 'surname' => 'Doe']
 */ ];
```

If `ARRAY_KEY` alias exists in resulting set - it's value will be added as array keys (and removed from resulting rows):

```php
$result => $driver->select("SELECT id AS ARRAY_KEY, * FROM users WHERE name = ?", ['John']);

/*  $result = [
 *      1 => ['name' => 'John', 'surname' => 'Smith'],
 *      2 => ['name' => 'John', 'surname' => 'Doe']
 */ ];
```

#### getRow()
```php
public function getRow($sql, array $args = []);
```

Runs SQL query and returns produced its first row as array.

```php
$result => $driver->getRow("SELECT * FROM users WHERE name = ?", ['John']);
```

#### getColumn()
```php
public function getColumn($sql, array $args = []);
```

Runs SQL query and returns produced its first column as array of one-element arrays.

```php
$result => $driver->getColumn("SELECT id FROM users WHERE name = ?", ['John']);

/*  $result = [
 *      1,
 *      2
 */ ];
```

`ARRAY_KEY` alias also works here:

```php
$result => $driver->getColumn("SELECT id AS ARRAY_KEY, surname FROM users WHERE name = ?", ['John']);

/*  $result = [
 *      1 => 'Smith',
 *      2 => 'Doe'
 */ ];
```

#### getCell()
```php
public function getCell($sql, array $args = []);
```

Runs SQL query and returns produced its first column of its first row:

```php
$result => $driver->getCell("SELECT COUNT(*) FROM users WHERE name = ?", ['John']);

//  $result = 2;
```


#### load()
```php
public function load($table, $id);
```

Shortcut to get values of a single row from given table by id:

```php
$result => $driver->load('users', 1);

//  $result = ['id' => 1, name' => 'John', 'surname' => 'Smith'];
```

#### insert()
```php
public function insert($table, array $data);
```

Inserts data into the table and returns inserted ID

```php
$result => $driver->insert('users', [
    'name' => 'John',
    'surname' => 'McKey'
]);

//  $result = 3;
```


#### update()
```php
public function update($table, $id, $data);
```

Updates existing data by row ID:

```php
$driver->update('users', [
    'name' => 'Mike',
], 1);
```

#### delete()
```php
public function delete($table, $id);
```

Deletes row with given ID:

```php
$driver->delete('users', 3);
```

# Entity

# Extending Entity

# Repository

# Extending Repository

# Collection

# EntityManager
