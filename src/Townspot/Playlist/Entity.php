<?php
namespace Townspot\Playlist;

class Entity extends \Townspot\Entity
{
	protected $_id;

	protected $_name;

	protected $_description;

	protected $_media;

	protected $_user;

	protected $_created;
	
	protected $_updated;

	public function __construct()
	{
		$this->_created = new \DateTime();
		$this->_updated = new \DateTime();
		$this->_media = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function setName($value)
	{
		$this->_name = $value;
		return $this;
	}

	public function setDescription($value)
	{
		$this->_description = $value;
		return $this;
	}

	public function addMedia(\Townspot\Media\Entity $value)
	{
		$this->_media->add($value);
		return $this;
	}
	
	public function removeMedia($key)
	{
		$this->_media->remove($key);
		return $this;
	}
	
	public function setUser(\Townspot\User\Entity $value)
	{
		$this->_user = $value;
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

	public function getName()
	{
		return $this->_name;
	}

	public function getDescription()
	{
		return $this->_description;
	}

	public function getMedia()
	{
		return $this->_media;
	}

	public function getUser()
	{
		return $this->_user;
	}

	public function getCreated()
	{
		return $this->_created;
	}

	public function getUpdated()
	{
		return $this->_updated;
	}

	public function getPlaylistLink()
	{
		return "/users/" . htmlentities(strtolower($this->getUser()->getUsername())) . "/" . htmlentities(strtolower($this->getName()));
	}
}