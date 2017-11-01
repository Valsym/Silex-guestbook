<?php

//define('MVCBOX_PUBLIC_ROOT', __DIR__);

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

spl_autoload_register(function( $className ) {
  // Namespace mapping
  $namespaces = array(
    "Art" => __DIR__ . "/controller/view",
    "Model" => __DIR__ . "/model"
  );

  foreach ( $namespaces as $ns => $path ) {
    if ( 0 === strpos( $className, "{$ns}\\" ) ) {
      $pathArr = explode( "\\", $className );
      $pathArr[0] = $path;

      $class = implode(DIRECTORY_SEPARATOR, $pathArr);

      require_once "{$class}.php";
    }
  }
});

//Controlelrs
foreach ( glob(__DIR__."/../src/MvcBox/controller/*.php") as $filename ) {
  require_once $filename;
}

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
