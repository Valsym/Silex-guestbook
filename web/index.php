<?php

define('MVCBOX_PUBLIC_ROOT', __DIR__);

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

require __DIR__.'/../app/config/dev.php';
require __DIR__.'/../src/app.php';
require __DIR__.'/../src/routes.php';

//HTML_QuickForm2
set_include_path(
  get_include_path() . PATH_SEPARATOR .
  __DIR__ . "/../vendor/QuickForm2"
);
require_once __DIR__ . '/../vendor/QuickForm2/HTML/QuickForm2.php';
require_once __DIR__ . '/../vendor/QuickForm2/HTML/QuickForm2/Renderer.php';

$app->run();
