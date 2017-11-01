<?php

namespace MvcBox\Models;

use Doctrine\DBAL\Connection;
use MvcBox\Entity\Like;

/**
 * Like repository
 */
class LikeRepository implements RepositoryInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;

    /**
     * @var \MvcBox\Repository\CommentRepository
     */
    protected $commentRepository;

    /**
     * @var \MvcBox\Repository\UserRepository
     */
    protected $userRepository;

    public function __construct(Connection $db, $commentRepository, $userRepository)
    {
        $this->db = $db;
        $this->commentRepository = $commentRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Saves the like to the database.
     *
     * @param \MvcBox\Entity\Like $like
     */
    public function save($like)
    {
        $likeData = array(
            'comment_id' => $like->getComment()->getId(),
            'user_id' => $like->getUser()->getId(),
        );

        if ($like->getId()) {
            $this->db->update('likes', $likeData, array('like_id' => $like->getId()));
        } else {
            // The like is new, note the creation timestamp.
            $likeData['created_at'] = time();
			$this->commentRepository->setLikes(0);

            $this->db->insert('likes', $likeData);
            // Get the id of the newly created like and set it on the entity.
            $id = $this->db->lastInsertId();
            $like->setId($id);
        }
    }

    /**
     * Deletes the like.
     *
     * @param integer $id
     */
    public function delete($id)
    {
        return $this->db->delete('likes', array('like_id' => $id));
    }

    /**
     * Returns the total number of likes.
     *
     * @return integer The total number of likes.
     */
    public function getCount() {
        return $this->db->fetchColumn('SELECT COUNT(like_id) FROM likes');
    }

    /**
     * Returns a like matching the supplied id.
     *
     * @param integer $id
     *
     * @return \MvcBox\Entity\Like|false A like if found, false otherwise.
     */
    public function find($id)
    {
        $likeData = $this->db->fetchAssoc('SELECT * FROM likes WHERE like_id = ?', array($id));
        return $likeData ? $this->buildLike($likeData) : FALSE;
    }

    /**
     * Returns a collection of likes for the given user id.
     *
     * @param integer $commentId
     *   The comment id.
     * @param integer $userId
     *   The user id.
     *
     * @return \MvcBox\Entity\Like|false A like if found, false otherwise.
     */
    public function findByCommentAndUser($commentId, $userId)
    {
        $conditions = array(
            'comment_id' => $commentId,
            'user_id' => $userId,
        );
        $likes = $this->getLikes($conditions, 1, 0);
        if ($likes) {
            return reset($likes);
        }
    }

    /**
     * Returns a collection of likes.
     *
     * @param integer $limit
     *   The number of likes to return.
     * @param integer $offset
     *   The number of likes to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of likes, keyed by like id.
     */
    public function findAll($limit, $offset = 0, $orderBy = array())
    {
        return $this->getLikes(array(), $limit, $offset, $orderBy);
    }

    /**
     * Returns a collection of likes for the given comment id.
     *
     * @param integer $commentId
     *   The comment id.
     * @param integer $limit
     *   The number of likes to return.
     * @param integer $offset
     *   The number of likes to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of likes, keyed by like id.
     */
    public function findAllByComment($commentId, $limit, $offset = 0, $orderBy = array())
    {
        $conditions = array(
            'comment_id' => $commentId,
        );
        return $this->getLikes($conditions, $limit, $offset, $orderBy);
    }

    /**
     * Returns a collection of likes for the given user id.
     *
     * @param $userId
     *   The user id.
     * @param integer $limit
     *   The number of likes to return.
     * @param integer $offset
     *   The number of likes to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of likes, keyed by like id.
     */
    public function findAllByUser($userId, $limit, $offset = 0, $orderBy = array())
    {
        $conditions = array(
            'user_id' => $userId,
        );
        return $this->getLikes($conditions, $limit, $offset, $orderBy);
    }

    /**
     * Returns a collection of likes for the given conditions.
     *
     * @param integer $limit
     *   The number of likes to return.
     * @param integer $offset
     *   The number of likes to skip.
     * @param $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of likes, keyed by like id.
     */
    protected function getLikes(array $conditions, $limit, $offset, $orderBy = array())
    {
        // Provide a default orderBy.
        if (!$orderBy) {
            $orderBy = array('created_at' => 'DESC');
        }

        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('l.*')
            ->from('likes', 'l')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('l.' . key($orderBy), current($orderBy));
        $parameters = array();
        foreach ($conditions as $key => $value) {
            $parameters[':' . $key] = $value;
            $where = $queryBuilder->expr()->eq('l.' . $key, ':' . $key);
            $queryBuilder->andWhere($where);
        }
        $queryBuilder->setParameters($parameters);
        $statement = $queryBuilder->execute();
        $likesData = $statement->fetchAll();

        $likes = array();
        foreach ($likesData as $likeData) {
            $likeId = $likeData['like_id'];
            $likes[$likeId] = $this->buildLike($likeData);
        }
        return $likes;
    }

    /**
     * Instantiates a like entity and sets its properties using db data.
     *
     * @param array $likeData
     *   The array of db data.
     *
     * @return \MvcBox\Entity\Like
     */
    protected function buildLike($likeData)
    {
        // Load the related comment and user.
        $comment = $this->commentRepository->find($likeData['comment_id']);
        $user = $this->userRepository->find($likeData['user_id']);

        $like = new Like();
        $like->setId($likeData['like_id']);
        $like->setComment($comment);
        $like->setUser($user);
        $createdAt = new \DateTime('@' . $likeData['created_at']);
        $like->setCreatedAt($createdAt);
        return $like;
    }
}
