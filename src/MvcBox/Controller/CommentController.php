<?php

namespace MvcBox\Controller;

use MvcBox\Entity\Comment;
use MvcBox\Entity\User;
use MvcBox\Entity\Like;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class CommentController
{

    public function viewAction(Request $request, Application $app)
    {
        $commentid = $request->attributes->get('id');
        if (!$commentid) {
            $app->abort(404, 'The requested comment was not found.');
        }
		
		$oldcomment =  $app['repository.comment']->find($commentid);
		if ($oldcomment) {
			$userid = $oldcomment->getUser()->getId();
			$user = $app['repository.user']->find($userid);

			$likes_by_comment = $app['repository.like']->findAllByComment($oldcomment->getId(), 100);
			$likecount = sizeof($likes_by_comment);
			$data = $this->rebuildComment($oldcomment, $likecount);

			return $app['view']->render('layout.phtml', 'index/review.phtml', array(
				'comment' => $data,
			));
		}
		
    }
	
	public function formAction(Request $request, Application $app)
    {
				
		$form = new \HTML_QuickForm2('review', 'post', array('action' => ""));
		$fieldSet = $form->addFieldset()->setLabel('Пожалуйста представьтесь и напишите ваш отзыв:');
		$name = $fieldSet->addText('username')->setLabel('Ваше имя:');
		$name->addRule('required', 'Пожалуйста назовитесь.');
		$email = $fieldSet->addText('mail')->setLabel('Ваш email (опубликован не будет)');
		$email->addRule('required', 'Пожалуйста укажите ваш E-mail.');
		$email->addRule('email', 'Email не корректен');
		$fieldSet->addElement(
			'textarea', 'content', array('style' => 'width: 300px;', 'cols' => 50, 'rows' => 7),
			array('label' => 'Текст отзыва (html-тэги будут вырезаны):'))
			->addRule('required', 'Поле обязательно для заполнения');
		$fieldSet->addElement('button', null, array('type' => 'submit'))
			->setContent('Отправить отзыв');


		if ( $form->isSubmitted() && $form->validate() ) {
			$values = $form->getValue();

			foreach ($values as $key => $value) {
				$$key = $value;
			}
			$ip = $_SERVER['REMOTE_ADDR'];
			if ($ip == '::1' || $_SERVER["SERVER_NAME"] = 'localhost') $ip = '127.0.0.1';
			
			$comment = new Comment();
			$user = new User();
			$user->setUsername($username);
			$user->setMail($mail);
			$user->setIp($ip);
			$existuser = $app['repository.user']->loadUserByUsername($username);
			if (!$existuser) {				
				$app['repository.user']->save($user);
			} else if ($existuser->getIp() != $ip) {
				$app['repository.user']->save($user);
			}
			
			$comment->setComment($content);
			$comment->setUser($user);
			$app['repository.comment']->save($comment);
		
			// redirect
			$fieldSet->addElement('fieldset')->setLabel('<br /><br />Спасибо, '.$username.'! <br>Ваш отзыв опубликован.<br />Сейчас вы будете перенаправлены на Главную страницу...<br />');
			$homepage = $app['url_generator']->generate('homepage');
			header( "Refresh:5; URL=$homepage" );
		}

		return $app['view']->render('layout.phtml', 'index/form.phtml', 
			array('form' => $form
		));
		
		
	}

    public function likeAction(Request $request, Application $app)
    {
        $id = $request->attributes->get('id');
		$ip = $request->attributes->get('ip');
		if ($ip == '::1' || $_SERVER["SERVER_NAME"] = 'localhost') $ip = '127.0.0.1';
		$like = $request->attributes->get('like');
		
		// если статья понравилась прибавляем один лайк в бд
		if ($like >= 0 && $id > 0) {
			$comment = $app['repository.comment']->find($id);
			$comment_id = $comment->getId();
			$user = $comment->getUser();
			if ( ($user_ip = $user->getIp()) == $ip) {
				$exit = 3;
				return $app->json($exit); // Автор комментария не может его лайкать
			}
			$likes = $app['repository.like']->findAllByComment($comment_id, 100); // берем все лайки комментария
			$ip_arr = [];

			foreach ($likes as $elike) {
				$ip_arr[] = $elike->getUser()->getIp(); // Собираем массив всех IP юзеров для данного комментария
			}
			$filtered = array_filter($ip_arr, function($v) use ($ip) {return !strcmp($v, $ip);});
			if (sizeof($filtered) > 0 && $filtered != [""]) {
				// если ip уже есть в списке, то отдаем ответ равный 2 (т.е. повторно с одного IP лайкать незьзя)
				if ($like == 0) {
					$exit = 3;
				} else {
					$exit = 2;
				}
				return $app->json($exit);
			}
			
			// Иначе, лайк с уникальным IP юзера добавляем в базу и обновляем запись в базе:
			$like = new Like();
			$like->setComment($comment_id);
			$like->setUser($user_ip);
			$app['repository.like']->save($like);

			return $app->json($exit); // отдаем ответ равный 1 (т.е. прибавляем один лайк на кнопку)
		}
		
		$exit = 4; // что-то пошло не так...
		$app->abort(404, 'Извините, но что-то пошло не так...');
		return $app->json($exit);
    }
	
    protected function sendNotification($comment, Application $app)
    {
        $artist = $comment->getArtist();
        $user = $comment->getUser();
        $messageBody = 'The following comment was posted by ' . $user->getUsername() . ":\n";
        $messageBody .= $comment->getComment();
        $message = \Swift_Message::newInstance()
            ->setSubject('New comment posted for artist ' . $artist->getName())
            ->setFrom(array($app['site_email']))
            ->setTo(array($app['admin_email']))
            ->setBody('The following comment was posted by :');
        $app['mailer']->send($message);
    }
	
	protected function rebuildComment($comment, $likes_by_comment)
	{
		$commentData = [];
		$commentData['comment_id'] = $comment->getId();
		$commentData['comment_username'] = $comment->getUser()->getUsername();
		$commentData['comment_userip'] = $comment->getUser()->getIp();
		$commentData['comment'] = $comment->getComment();
		$commentData['created_at'] = $comment->getCreatedAt()->format('d/m/Y');
		$userid = $comment->getUser()->getId();
		$commentData['likes'] = $likes_by_comment;
		return $commentData;

	}
	
}
