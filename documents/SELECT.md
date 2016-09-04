SELECT DOCUMENT
===============
SELECT syntax is slightly more complicated.

Example Database Server
-------------
___Server Information___
> _address_: `127.0.0.1`
>
> _port_: `3306`
>
> _username_: `root`
>
> _password_: `root`
>
> _database_: `test`

Table structure and data
------------------
> _table name_: `prefix_students`

| id             | name           | password       | email          |
| :------------- | :------------- | :------------- | :------------- |
| 1              | Kim            | Kim_Password   | Kim@email.com  |
| 2              | Amy            | Amy_Password   | Amy@email.com  |
| 3              | Lily           | Lily_Password  | Lily@email.com |
| 4              | John           | John_Password  | John@email.com |

Helper initialize
-----------------
> `Helper Document`: [Helper Document](https://github.com/JShadowMan/SDB/blob/master/documents/Helper.md)

```php
<?php

# Import Helper
use SDB\Helper;

# Add database server
Helper::server('127.0.0.1', '3306', 'root', 'root', 'test');
```

SELECT Methods
--------------

- `select(...)`
> _parameter is query fields_
>
> __NOTE__: if parameter is empty, equal to query all fields
>
> can using `array($field, $alias)` as a field alias

```php
<?php
# select * from table
select()->...

# select `field1`, `field2` from table
select('field1', 'field2')->...

# select `field1` as `alias`, `field2` from table
select(array('field1', 'alias'), 'field2')->...
```

___Example___
```php
<?php

# Create Helper instance
$helper = new Helper('prefix_');

# query
$helper->select(array('id', 'uid'), 'name', array('email', 'mail'))->query();

# Getting all query data
$result = $helper->fetchAll();

# Result like follows
$result = array(
    0 => array(
        'uid'  => 1,
        'name' => 'Kim',
        'mail' => 'Kim@email.com'
    ),
    1 => array(
        'uid'  => 2,
        'name' => 'Amy',
        'mail' => 'Amy@email.com'
    ),
    ...
)
```
