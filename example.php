<?php
require_once 'src/Db.php';

$db = new Db();

echo $db->init();
exit;