<?php
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

$app->get('/', function () use ($app) {
    // forward to /reviews
    $subRequest = Request::create('/reviews/0/0', 'GET');

    return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
})->bind('/');

$app->get('/reviews/{sort1}/{sort2}', function ($sort1, $sort2) use ($app) {
	$sql = "SELECT * FROM guestbook.reviews";
    $reviews = $app['db']->fetchAll($sql);
	if ($sort1 == 1 || $sort1 == 2 || $sort2 == 1 || $sort2 == 2) {
		foreach ($reviews as $key => $row) {
			$ddate[$key]  = $row['date'];
			$like[$key]  = $row['likes'];
		}
		$sort1 == 1 ? array_multisort($ddate, SORT_ASC, $reviews) : '';
		$sort1 == 2 ? array_multisort($ddate, SORT_DESC, $reviews) : '';
		$sort2 == 1 ? array_multisort($like, SORT_ASC, $reviews) : '';
		$sort2 == 2 ? array_multisort($like, SORT_DESC, $reviews) : '';
	}
	return $app['view']->render('layout.phtml', 'index/reviews.phtml', array(
		'reviews' => $reviews));
})->bind('reviews');

$app->get('/review/{id}', function ($id) use ($app) {
	$sql = "SELECT * FROM guestbook.reviews where id = ?";
	$review = $app['db']->fetchAssoc($sql, array((int) $id));
	if ( !$review ) {
		$app->abort(404, "Отзыв {$id} не существует.");
	}

	return $app['view']->render('layout.phtml', 'index/review.phtml', array(
		'review' => $review
	));
})->bind('review');

$app->get('/like/{id}/{ip}/{like}', function ($id, $ip, $like) use ($app) {
	// если статья понравилась прибавляем один лайк в бд
	$sql = "SELECT * FROM guestbook.reviews where id = ?";
	$review = $app['db']->fetchAssoc($sql, array((int) $id));
	if ($like >= 0 && $id >= 0) {
		if ($ip == '::1' || $_SERVER["SERVER_NAME"] = 'localhost') $ip = '127.0.0.1';
		$ip_str = $review['authorIP'];
		$ip_arr = explode(',', $ip_str);
		$filtered = array_filter($ip_arr, function($v) use ($ip) {return !strcmp($v, $ip);});
		if (sizeof($filtered) > 0 && $filtered != [""]) {
			// если ip уже есть в списке, то отдаем ответ равный 2
			$exit = 2;
			return $app->json($exit);
		}
		
		$ip_str .= ','.$ip; // уникальный IP добавляем в базу и обновляем запись в базе
		$sql = "UPDATE guestbook.reviews SET likes = :likes, authorIP = :authorIP WHERE id = :id";
		$app['db']->executeUpdate($sql, array(			 
			'likes' => (int) $like + 1,
			'authorIP' => $ip_str,
			'id' => $id
		));
		$exit = 1;
		
		return $app->json($exit);
	}
	$exit = 3;

	return $app->json($exit);
})->bind('like');

$app->match('/form', function () use ($app) {
	$form = new HTML_QuickForm2('review', 'post', array('action' => ""));
	$form->addElement('text', 'author')
	->setlabel('Имя автора')
	->addRule('required', 'Поле обязательно для заполнения');

	$form->addElement(
    'textarea', 'content', array('style' => 'width: 300px;', 'cols' => 50, 'rows' => 7),
    array('label' => 'Текст отзыва (html-тэги запрещены):'))
	->addRule('required', 'Поле обязательно для заполнения');

	$form->addElement('button', null, array('type' => 'submit'))
	->setContent('Отправить отзыв');

	if ( $form->isSubmitted() && $form->validate() ) {
		$values = $form->getValue();
	
		$ip = $_SERVER['REMOTE_ADDR'];
		if ($ip == '::1' || $_SERVER["SERVER_NAME"] = 'localhost') $ip = '127.0.0.1';
		
		$app['db']->insert('guestbook.reviews', 
			array(
				'id' => '',
				'date' => date('Y-m-d'), 
				'author' => $values['author'], 
				'authorIP' => $ip, 
				'content' => strip_tags($values['content']), // запрет html-тегов (они просто вырежатся)
				'likes' => 0
			)
		);
		
		// redirect
		$autor = $values['author'];
		$subRequest = Request::create("/thanks/$autor", 'GET');
		return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
	}

	return $app['view']->render('layout.phtml', 'index/form.phtml', 
		array('form' => $form
	));
})->bind('form');

$app->get('/thanks/{author}', function ($author) use ($app) {

	return $app['view']->render('layout.phtml', 'index/thanks.phtml', array(
		'author' => $author
	));
});

$app->error(function (\Exception $e)  use ($app) {
    switch ($e->getStatusCode()) {
        case 404:
            $message = 'Ошибка 404: такой страницы не существует.';
            break;
        case 400:
            $message = $e->getMessage();
            break;
        default:
            $message = 'Извинтете, но что-то пошло не так...';
    }
	return $app['view']->render('layout.phtml', 'index/error.phtml', array(
    'message' => $message
  ));
});
