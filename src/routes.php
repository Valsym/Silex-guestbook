<?php

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
