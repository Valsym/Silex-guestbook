<?php

namespace MvcBox\Models;

use Doctrine\DBAL\Connection;
use MvcBox\Entity\Comment;

/**
 * Comment repository
 */
class CommentRepository implements RepositoryInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;


    /**
     * @var \MvcBox\Repository\UserRepository
     */
    protected $userRepository;


    public function __construct(Connection $db, $userRepository)
    {
        $this->db = $db;
        $this->userRepository = $userRepository;
    }

    /**
     * Saves the comment to the database.
     *
     * @param \MvcBox\Entity\Comment $comment
     */
    public function save($comment)
    {
        $commentData = array(
            'user_id' => $comment->getUser()->getId(),
            'comment' => $comment->getComment(),
        );

        if ($comment->getId()) {
            $this->db->update('comments', $commentData, array('comment_id' => $comment->getId()));
        } else {
            // The comment is new, note the creation timestamp.
            $commentData['created_at'] = time();

            $this->db->insert('comments', $commentData);
            // Get the id of the newly created comment and set it on the entity.
            $id = $this->db->lastInsertId();
            $comment->setId($id);
        }
    }

    /**
     * Deletes the comment.
     *
     * @param integer $id
     */
    public function delete($id)
    {
        return $this->db->delete('comments', array('comment_id' => $id));
    }

    /**
     * Returns the total number of comments.
     *
     * @return integer The total number of comments.
     */
    public function getCount() {
        return $this->db->fetchColumn('SELECT COUNT(comment_id) FROM comments');
    }

    /**
     * Returns a comment matching the supplied id.
     *
     * @param integer $id
     *
     * @return \MvcBox\Entity\Comment|false An entity object if found, false otherwise.
     */
    public function find($id)
    {
        $commentData = $this->db->fetchAssoc('SELECT * FROM comments WHERE comment_id = ?', array($id));
        return $commentData ? $this->buildComment($commentData) : FALSE;
    }

    /**
     * Returns a collection of comments.
     *
     * @param integer $limit
     *   The number of comments to return.
     * @param integer $offset
     *   The number of comments to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of comments, keyed by comment id.
     */
    public function findAll($limit, $offset = 0, $orderBy = array())
    {
        return $this->getComments(array(), $limit, $offset, $orderBy);
    }

    /**
     * Returns a collection of comments for the given comment id.
     *
     * @param $userId
     *   The comment id.
     * @param integer $limit
     *   The number of comments to return.
     * @param integer $offset
     *   The number of comments to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of comments, keyed by comment id.
     */
    public function findAllByUser($userId, $limit, $offset = 0, $orderBy = array())
    {
        $conditions = array(
            'user_id' => $userId,
            //'published' => TRUE,
        );
        return $this->getComments($conditions, $limit, $offset);
    }

    /**
     * Returns a collection of comments for the given conditions.
     *
     * @param integer $limit
     *   The number of comments to return.
     * @param integer $offset
     *   The number of comments to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of comments, keyed by comment id.
     */
    protected function getComments(array $conditions, $limit, $offset, $orderBy = array())
    {
        // Provide a default orderBy.
        if (!$orderBy) {
            $orderBy = array('created_at' => 'DESC');
        }

        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('c.*')
            ->from('comments', 'c')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('c.' . key($orderBy), current($orderBy));
        $parameters = array();
        foreach ($conditions as $key => $value) {
            $parameters[':' . $key] = $value;
            $where = $queryBuilder->expr()->eq('c.' . $key, ':' . $key);
            $queryBuilder->andWhere($where);
        }
        $queryBuilder->setParameters($parameters);
        $statement = $queryBuilder->execute();
        $commentsData = $statement->fetchAll();

        $comments = array();
        foreach ($commentsData as $commentData) {
            $commentId = $commentData['comment_id'];
            $comments[$commentId] = $this->buildComment($commentData);
        }

        return $comments;

    }

    /**
     * Instantiates a comment entity and sets its properties using db data.
     *
     * @param array $commentData
     *   The array of db data.
     *
     * @return \MvcBox\Entity\Comment
     */
    protected function buildComment($commentData)
    {
        // Load the related comment and user.
        $user = $this->userRepository->find($commentData['user_id']);
        $comment = new Comment();
        $comment->setId($commentData['comment_id']);
        $comment->setUser($user);
        $comment->setComment($commentData['comment']);
        $createdAt = new \DateTime('@' . $commentData['created_at']);
        $comment->setCreatedAt($createdAt);

        return $comment;
    }
}
