<?php
require_once 'src/Helper.php';

$db = new Helper();

echo $db->init();
exit;