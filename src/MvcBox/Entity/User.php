<?php

namespace MvcBox\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;


class User 
{
    /**
     * User id.
     *
     * @var integer
     */
    protected $id;

    /**
     * Username.
     *
     * @var string
     */
    protected $username;


    /**
     * Email.
     *
     * @var string
     */
    protected $mail;

    /**
     * When the User entity was created.
     *
     * @var DateTime
     */
    protected $createdAt;

	
	/**
     * User IP.
     *
     * @var string
     */
	protected $ip;
	

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
	
	public function getIp()
    {
        return $this->ip;
    }

    public function setIp($ip)
    {
        $this->ip = $ip;
    }

   
    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getMail()
    {
        return $this->mail;
    }

    public function setMail($mail)
    {
        $this->mail = $mail;
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
