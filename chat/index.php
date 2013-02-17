<?php
include 'init.php';
include 'configs/config.php';

$m = isset($_GET['m']) ? $_GET['m'] : 'Index';

if(ord($m) < 65 || ord($m) > 122) $m = 'Index';

$m = ucfirst($m) . 'Controller';

$ct = new $m();

$ct->run();
