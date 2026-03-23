<?php

require_once __DIR__ . '/autoload.php';

$auth = new Auth();
$auth->logout();

header('Location: index.php');
exit;
