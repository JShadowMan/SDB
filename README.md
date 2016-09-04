SDB
===================
Database query helper. Simple, powerful and expandability.

[![Build Status](https://travis-ci.org/JShadowMan/SDB.svg?branch=master)](https://travis-ci.org/JShadowMan/SDB)
[![Coverage Status](https://coveralls.io/repos/github/JShadowMan/SDB/badge.svg?branch=master)](https://coveralls.io/github/JShadowMan/SDB?branch=master)
[![Latest Stable Version](https://poser.pugx.org/jshadowman/sdb/v/stable)](https://packagist.org/packages/jshadowman/sdb)
[![License](https://poser.pugx.org/jshadowman/sdb/license)](https://packagist.org/packages/jshadowman/sdb)
```php
<?php

# Import SDB packages
use SDB\Helper;

# Add database server
Helper::server('server.address', '3306', 'user', 'password', 'database');

# Create Helper instance. The first parameter is the table prefix
$helper = new Helper('table_');

# Execute SELECT query
$helper->select()->from('table.name')->query();

# Getting query data
$result = $helper->fetchAll();

# $result like follows
$result = array(
    0 => array(
        'field_1' => 'data',
        'field_2' => 'data',
        ...
    ),
    1 => array(
        'field_1' => 'data',
        'field_2' => 'data',
        ...
    ),
    ...
)
```

Table of Contents
---
- [Requirements](#requirements)
- [Installation](#installation)
- [Features](#features)
- [Usage](#usage)
- [License](#license)


Requirements
------------
SDB requires the following to run
- `PHP`: 5.4 or greater

Installation
------------
Installation is possible using Composer
```
composer require jshadowman/sdb
```

Features
--------
- List of Programming
- Automatically generated query
- Humanization design
- Expandability
- PSR4 standard
- Secure

Usage
-----
- **SELECT**

Simple Example like following. [Complete SELECT documentation]

```php
<?php

# Import SDB packages
use SDB\Helper;

# Add database server
Helper::server('server.address', '3306', 'user', 'password', 'database');

# Create Helper instance. The first parameter is the table prefix
$helper = new Helper('table_');

# Execute SELECT query
$helper->select()->from('table.students')->order('name')->limit(5)->query();
```

- **INSERT**

Simple Example like following. [Complete INSERT documentation]

```php
<?php

# Import SDB packages
use SDB\Helper;

# Add database server
Helper::server('server.address', '3306', 'user', 'password', 'database');

# Create Helper instance. The first parameter is the table prefix
$helper = new Helper('table_');

# Execute SELECT query
$helper->insert('table.students')->rows(array(
    'name'   => 'Kim',
    'age'    => '10',
    'gender' => 'female'
))->query();
```


- **UPDATE**

Simple Example like following. [Complete UPDATE documentation]

```php
<?php

# Import SDB packages
use SDB\Helper;
use SDB\Expression;

# Add database server
Helper::server('server.address', '3306', 'user', 'password', 'database');

# Create Helper instance. The first parameter is the table prefix
$helper = new Helper('table_');

# Execute SELECT query
$helper->update('table.students')->set(array(
    'name' => 'Amy'
))->where(Expression::equal('name', 'Kim'))->query();
```

- **DELETE**

Simple Example like following. [Complete DELETE documentation]

```php
<?php

# Import SDB packages
use SDB\Helper;
use SDB\Expression;

# Add database server
Helper::server('server.address', '3306', 'user', 'password', 'database');

# Create Helper instance. The first parameter is the table prefix
$helper = new Helper('table_');

# Execute SELECT query
$helper->delete('table.students')->where(Expression::equal('name', 'Amy'))->query();
```

[Complete SELECT documentation]: https://github.com/JShadowMan/SDB/blob/master/documents/SELECT.md
[Complete INSERT documentation]: https://github.com/JShadowMan/SDB/blob/master/documents/INSERT.md
[Complete UPDATE documentation]: https://github.com/JShadowMan/SDB/blob/master/documents/UPDATE.md
[Complete DELETE documentation]: https://github.com/JShadowMan/SDB/blob/master/documents/DELETE.md

License
-------
SDB is licensed under the [MIT] license.
Copyright (C) 2016, ChengJie Wang

[MIT]: https://github.com/JShadowMan/SDB/blob/master/LICENSE
