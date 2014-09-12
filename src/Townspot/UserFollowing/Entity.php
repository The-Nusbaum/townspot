<?php
namespace Townspot\UserFollowing;

class Entity
{
	protected $_id;

	protected $_share_email;

	protected $_user;

	protected $_follower;

	public function __construct()
	{
	}

	public function setShareEmail($value)
	{
		$this->_share_email = $value;
		return $this;
	}

	public function setUser(\Townspot\User\Entity $value)
	{
		$this->_user = $value;
		return $this;
	}

	public function setFollower(\Townspot\User\Entity $value)
	{
		$this->_follower = $value;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function getShareEmail()
	{
		return (bool)$this->_share_email;
	}

	public function getUser()
	{
		return $this->_user;
	}

	public function getFollower()
	{
		return $this->_follower;
	}
}