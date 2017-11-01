<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Register service providers.
$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

// Register repositories.
$app['repository.user'] = $app->share(function ($app) {
    return new MvcBox\Models\UserRepository($app['db']);
});
$app['repository.comment'] = $app->share(function ($app) {
    return new MvcBox\Models\CommentRepository($app['db'], $app['repository.user']);
});
$app['repository.like'] = $app->share(function ($app) {
    return new MvcBox\Models\LikeRepository($app['db'], $app['repository.comment'], $app['repository.user']);
});

// Register custom services.
$app['urlgen'] = function($app) {
    return new MvcBox\Service\Urlgen($app);
};

$app['view'] = function($app) use ($app) {
    return new MvcBox\Service\View($app);
};


// Register the error handler.
$app->error(function (\Exception $e)  use ($app) {
	if (method_exists('getStatisCode', $e)) {	
		switch ($e->getStatusCode()) {
			case 404:
				$message = 'Ошибка 404: такой страницы не существует.';
				break;
			case 400:
				$message = $e->getMessage();
				break;
			default:
				$message = 'Извините, но что-то пошло не так...';
		}
	} else {
		$message = 'Извините, но похоже, такой страницы не существует.';
	}
	return $app['view']->render('layout.phtml', 'index/error.phtml', array(
    'message' => $message
  ));
});

return $app;
