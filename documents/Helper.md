Helper DOCUMENT
===============


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
------------------------
__table name__: `prefix_students`

| id             | name           | password       | email          |
| :------------- | :------------- | :------------- | :------------- |
| 1              | Kim            | Kim_Password   | Kim@email.com  |
| 2              | Amy            | Amy_Password   | Amy@email.com  |
| 3              | Lily           | Lily_Password  | Lily@email.com |
| 4              | John           | John_Password  | John@email.com |



Methods
-------
### `server($host, $port, $user, $password, $database)`

> Add a server
>
> | Parameter      | Type           | Explain        
> | :------------- | :------------- | :-------------
> | $host          | ___string___   | Server address
> | $port          | ___string___   | Server listening port
> | $user          | ___string___   | User name
> | $password      | ___string___   | User password
> | $database      | ___string___   | Database name

___Example___

```php
<?php

# Import Helper
use SDB\Helper;

# Add mysql server
Helper::server('127.0.0.1', '3306', 'root', 'root', 'test');
```

### `__constructor($prefix, $adapter = null)`

> Helper constructor
>
> | Parameter      | Type           | Explain        
> | :------------- | :------------- | :-------------
> | $prefix        | ___string___   | table prefix
> | $adapter       | ___string___   | database adapter
> List of adapter constant
> - `ADAPTER_MYSQL`
> - `ADAPTER_PDO_MYSQL`
> - `ADAPTER_ORACLE`
> - `ADAPTER_PDO_ORACLE`
> - `ADAPTER_PGSQL`
> - `ADAPTER_PDO_PGSQL`
> - `ADAPTER_SQL_SERVER`
> - `ADAPTER_SQLITE`


### `select(...)`
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
