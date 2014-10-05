<?php
namespace Townspot\UserEvent;

class Entity extends \Townspot\Entity
{
	protected $_id;

	protected $_title;
	
	protected $_url;
	
	protected $_description;
	
	protected $_artistname;
	
	protected $_created;
	
	protected $_updated;
	
	protected $_start;
	
	protected $_user;

	public function __construct()
	{
		$this->_created = new \DateTime();
		$this->_updated = new \DateTime();
		$this->_start = new \DateTime();
	}

	public function setTitle($value)
	{
		$this->_title = $value;
		return $this;
	}
	
	public function setUrl($value)
	{
		$this->_url = $value;
		return $this;
	}

	public function setDescription($value)
	{
		$this->_description = $value;
		return $this;
	}
	
	public function setArtistName($value)
	{
		$this->_artistname = $value;
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
	
	public function setStart(\DateTime $value)
	{
		$this->_start = $value;
		return $this;
	}
	
	public function setUser(\Townspot\User\Entity $value)
	{
		$this->_user = $value;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function getTitle()
	{
		return $this->_title;
	}
	
	public function getUrl()
	{
		return $this->_url;
	}

	public function getDescription()
	{
		return $this->_description;
	}
	
	public function getArtistName()
	{
		return $this->_artistname;
	}
	
	public function getCreated()
	{
		return $this->_created;
		return $this;
	}

	public function getUpdated()
	{
		return $this->_updated;
	}
	
	public function getStart()
	{
		return $this->_start;
	}
	
	public function getUser()
	{
		return $this->_user;
	}
}