<?php
$loader = include __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4('SDB\\', array('src', 'tests/SDBTest'));
