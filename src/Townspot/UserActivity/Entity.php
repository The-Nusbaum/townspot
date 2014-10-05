<?php
namespace Townspot\UserActivity;

class Entity extends \Townspot\Entity
{
	protected $_id;

	protected $_action;

	protected $_value;

	protected $_created;

	protected $_updated;

	protected $_user;

	protected $_artist;

	protected $_media;

	public function __construct()
	{
		$this->_created = new \DateTime();
		$this->_updated = new \DateTime();
	}

	public function setAction($value)
	{
		$this->_action = $value;
		return $this;
	}

	public function setValue($value)
	{
		$this->_value = $value;
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

	public function setUser(\Townspot\User\Entity $value)
	{
		$this->_user = $value;
		return $this;
	}

	public function setArtist(\Townspot\User\Entity $value)
	{
		$this->_artist = $value;
		return $this;
	}

	public function setMedia(\Townspot\Media\Entity $value)
	{
		$this->_media = $value;
		return $this;
	}
	
	public function getId()
	{
		return $this->_id;
	}

	public function getAction()
	{
		return $this->_action;
	}

	public function getValue()
	{
		return $this->_value;
	}

	public function getCreated()
	{
		return $this->_created;
	}

	public function getUpdated()
	{
		return $this->_updated;
	}

	public function getUser()
	{
		return $this->_user;
	}

	public function getArtist()
	{
		return $this->_artist;
	}

	public function getMedia()
	{
		return $this->_media;
	}
}