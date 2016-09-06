<?php

$protocol = 'http://';
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
	$protocol = 'https://';
return array(
	'HTTPHOST' => 'localhost',
	'BASEURL' => '/gradlead/code',
	'DBHOST' => 'localhost',
	'DBNAME' => 'smartjob',
	'DBUSER' => 'root',
	'DBPASSWORD' => 'password',
	'DBADAPTER' => 'Pdo_Mysql',
	'MYSQL_CHARSET' => 'utf8',
	'SITE_URL' => $protocol . $_SERVER['HTTP_HOST'] . '/gradlead/code',
	'USER_SITE_URL' => $protocol . $_SERVER['HTTP_HOST'] . '/gradlead/code',
	'ADMIN_SITE_URL' => $protocol . $_SERVER['HTTP_HOST'] . '/gradlead/code/admin',
);