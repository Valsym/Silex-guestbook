<?php

namespace MvcBox\Controller;

use Silex\Application;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class IndexController
{
    public function indexAction(Request $request, Application $app)
    {
        $count = $app['repository.comment']->getCount();
		$limit = 20 > $count ? $count : 20;
        $offset = 0;
        $newestOrderBy = array('created_at' => 'DESC');
        $newestComments = $app['repository.comment']->findAll($limit, $offset, $newestOrderBy);

		$data = [];
		foreach ( $newestComments as $comment ) {
			$commentid = $comment->getId();
			$likes_by_comment = $app['repository.like']->findAllByComment($comment->getId(), 100);
			$likecount = sizeof($likes_by_comment);
			$data[] = $this->rebuildComment($comment, $likecount);
		}

		return $app['view']->render('layout.phtml', 'index/reviews.phtml', array(
		'comments' => $data));

    }
	
    public function sortAction(Request $request, Application $app)
    {
		$sort1 = $request->attributes->get('sort1');
		$sort2 = $request->attributes->get('sort2');

		$orderBy = [];
		$sort1 == 1 ? $orderBy = array('created_at' => 'ASC') : '';
		$sort1 == 2 ? $orderBy = array('created_at' => 'DESC') : '';
		if ($sort1 == 0 && $sort2 == 0) $orderBy = array('created_at' => 'DESC');
			
        $total = $app['repository.comment']->getCount();
		$limit = 20 > $total ? $total : 20;
        $offset = 0;
        $sortComments = $app['repository.comment']->findAll($limit, $offset, $orderBy);
		
		$data = [];
		foreach ( $sortComments as $comment ) {
			$commentid = $comment->getId();
			$likes_by_comment = $app['repository.like']->findAllByComment($comment->getId(), 100);
			$likecount = sizeof($likes_by_comment);
			$data[] = $this->rebuildComment($comment, $likecount);
		}
		
		if ($sort2 == 1 || $sort2 == 2) {
			foreach ($data as $key => $row) {
				$like[$key]  = $row['likes'];
			}
			$sort2 == 1 ? array_multisort($like, SORT_ASC, $data) : '';
			$sort2 == 2 ? array_multisort($like, SORT_DESC, $data) : '';
		}

		return $app['view']->render('layout.phtml', 'index/reviews.phtml', array(
		'comments' => $data));

    }	


	protected function rebuildComment($comment, $likes_by_comment)
	{
		// Load the related comment and user.
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
