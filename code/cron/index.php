<?php

chdir(__DIR__ . '/..');

$config = @include 'config.php';

if (empty($_SERVER['REQUEST_METHOD'])) {
	$_SERVER['REQUEST_METHOD'] = 'GET';
}
$_SERVER['REQUEST_URI'] = rtrim($config['BASEURL'], '/') . '/system/miscellaneous/task_scheduler/';
if (empty($_SERVER['HTTP_HOST'])) {
	$_SERVER['HTTP_HOST'] = $config['HTTPHOST'];
}
if (empty($_SERVER['REMOTE_ADDR'])) {
	$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
}

require_once 'index.php';
