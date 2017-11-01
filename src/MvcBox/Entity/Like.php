<?php

namespace MvcBox\Entity;

class Like
{
    /**
     * Like id
     *
     * @var integer
     */
    protected $id;

    /**
     * Comment
     *
     * @var \MvcBox\Entity\Comment
     */
    protected $comment;

    /**
     * User
     *
     *  @var \MvcBox\Entity\User
     */
    protected $user;

    /**
     * When the like entity was created.
     *
     * @var DateTime
     */
    protected $createdAt;
	

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }
}
