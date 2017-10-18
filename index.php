<?php
// 
Подключаем Silex из директории vendor 
require_once __DIR__ . '/vendor/autoload.php';

$app = new Silex\Application();

if ( 'localhost' == $_SERVER["SERVER_NAME"] ) { // вывод ошибок для локальной версии
  $app['debug'] = true;
}

spl_autoload_register(function( $className ) {// Для более простого подключение вендоров, а также для будущего подключения моделей, 
	//добавим функцию автолоад
  // Namespace mapping
  $namespaces = array(
    "Art" => __DIR__ . "/controller/view",
    "Model" => __DIR__ . "/model",
	"Urlgen" => __DIR__ . "/controller/urlgen"
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

$app['view'] = function($app) { // подключаем обработчик шаблонов в виде сервиса
    return new Art\View($app);
};
$app['urlgen'] = function($app) { // подключаем url-генератор (абсолютные урлы)
    return new Urlgen\Urlgen($app);
};

// UrlGenerator // подключаем url-генератор (относительные урлы)
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

//Controlelrs - подключаем все контроллеры из директории controller.
foreach ( glob(__DIR__."/controller/*.php") as $filename ) {
  require_once $filename;
}

//подключение Doctrine 2, для работы с базой данных
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   	=> 'pdo_mysql',
		'host'      => 'localhost',
		'port' 		=> '3306',
        'dbname'    => 'guestbook',
		'user'      => 'root',
		'password'  => '',
		'charset'   => 'utf8'
    ),
));

//HTML_QuickForm2 
//Качаем http://pear.php.net/package/HTML_QuickForm2/redirected в vendor и подключаем:
set_include_path(
  get_include_path() . PATH_SEPARATOR .
  __DIR__ . "/vendor/QuickForm2"
);
require_once __DIR__ . '/vendor/QuickForm2/HTML/QuickForm2.php';
require_once __DIR__ . '/vendor/QuickForm2/HTML/QuickForm2/Renderer.php';

$app->run();
