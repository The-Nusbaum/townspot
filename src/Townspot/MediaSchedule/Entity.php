<?php
namespace Townspot\MediaSchedule;

class Entity
{
	protected $_id;

	protected $_media;

	protected $_created;

	protected $_updated;

	protected $_debut_time;

	public function __construct()
	{
		$this->_created = new \DateTime();
		$this->_updated = new \DateTime();
		$this->_debut_time = new \DateTime();
	}

	public function setMedia(\Townspot\Media\Entity $value)
	{
		$this->_media = $value;
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

	public function setDebutTime(\DateTime $value)
	{
		$this->_debut_time = $value;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function getMedia()
	{
		return $this->_media;
	}

	public function getCreated()
	{
		return $this->_created;
	}

	public function getUpdated()
	{
		return $this->_updated;
	}

	public function getDebutTime()
	{
		return $this->_debut_time;
	}
}