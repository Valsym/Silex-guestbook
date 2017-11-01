<?php

// Register route converters.
// Each converter needs to check if the $id it received is actually a value,
// as a workaround for https://github.com/silexphp/Silex/pull/768.
// $app['controllers']->convert('comment', function ($id) use ($app) {
    // if ($id) {
        // return $app['repository.comment']->find($id);
    // }
// });
// $app['controllers']->convert('user', function ($id) use ($app) {
    // if ($id) {
        // return $app['repository.user']->find($id);
    // }
// });

// Register routes.
$app->get('/', 'MvcBox\Controller\IndexController::indexAction')
    ->bind('homepage');

$app->get('/comments', 'MvcBox\Controller\IndexController::indexAction')
    ->bind('comments');
	
$app->get('/comments/{sort1}/{sort2}', 'MvcBox\Controller\IndexController::sortAction');
    //->bind('comments');
	
$app->match('/comment/{id}', 'MvcBox\Controller\CommentController::viewAction')
    ->bind('comment');
	
$app->match('/form', 'MvcBox\Controller\CommentController::formAction')
    ->bind('form');

$app->get('/like/{id}/{ip}/{like}', 'MvcBox\Controller\CommentController::likeAction')
    ->bind('like');
