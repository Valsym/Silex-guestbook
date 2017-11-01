<?php

// Timezone.
date_default_timezone_set('Europe/Moscow');

// Emails.
$app['admin_email'] = 'noreply@gbook.com';
$app['site_email'] = 'noreply@gbook.com';

// Doctrine (db)
$app['db.options'] = array(
    'driver'   => 'pdo_mysql',
    'host'     => '127.0.0.1',
    'port'     => '3306',
    'dbname'   => 'gbook',
    'user'     => 'root',
    'password' => '',
	'charset'   => 'utf8'
);

