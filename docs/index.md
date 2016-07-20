# Db abstractions library

Yet another DBAL inspired by Laravel collections.

MySQL and SQLite are supported for now.

It operates the following entities:

 * **Driver** - DB-specific layer, which can be used directly for non-standard requests

 * **Entity** - simple ActiveRecord DB table's row abstraction

 * **Collection** - set of entities, upon which array-related operations (ex: filtering, map/reduce, slicing) can be produced

 * **EntityManager** - Symfony-style object to Control Them All.

Library was especially designed to be "silent", no internal exceptions are thrown. Functions return `null`/`[]` if anything happens.
