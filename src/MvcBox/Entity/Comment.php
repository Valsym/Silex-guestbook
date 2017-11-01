<?php

namespace MvcBox\Entity;

class Comment
{
    /**
     * Comment id.
     *
     * @var integer
     */
    protected $id;


    /**
     * User.
     *
     *  @var \MvcBox\Entity\User
     */
    protected $user;

    /**
     * Comment.
     *
     * @var string
     */
    protected $comment;


    /**
     * When the comment entity was created.
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

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
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
