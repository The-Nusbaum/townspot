<?php
namespace Townspot\ArtistComment;

class Entity
{
	protected $_id;

	protected $_user;
	
	protected $_target;

	protected $_comment;

	protected $_created;

	protected $_updated;

	public function __construct()
	{
	}

	public function setUser(\Townspot\User\Entity $value)
	{
		$this->_user = $value;
		return $this;
	}

	public function setTarget(\Townspot\User\Entity $value)
	{
		$this->_target = $value;
		return $this;
	}

	public function setComment($value)
	{
		$this->_comment = $value;
		return $this;
	}

	public function setCreated(\DateTime $value)
	{
		$this->_created = $value;
		return $this;
	}

	public function setUpdated(\DateTime $value)
	{
		$this->_updated = $value;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function getUser()
	{
		return $this->_user;
	}

	public function getTarget()
	{
		return $this->_target;
	}

	public function getComment()
	{
		return $this->_comment;
	}

	public function getCreated()
	{
		return $this->_created;
	}

	public function getUpdated()
	{
		return $this->_updated;
	}
}