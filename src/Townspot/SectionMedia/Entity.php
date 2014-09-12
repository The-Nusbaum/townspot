<?php
namespace Townspot\SectionMedia;

class Entity
{
	protected $_id;

	protected $_section_block;
	
	protected $_media;

	protected $_priority;

	protected $_created;

	protected $_updated;

	public function __construct()
	{
		$this->_created = new \DateTime();
		$this->_updated = new \DateTime();
	}

	public function setSectionBlock(\Townspot\SectionBlock\Entity $value)
	{
		$this->_section_block = $value;
		return $this;
	}

	public function setMedia(\Townspot\Media\Entity $value)
	{
		$this->_media = $value;
		return $this;
	}

	public function setPriority($value)
	{
		$this->_priority = $value;
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

	public function getSectionBlock()
	{
		return $this->_section_block;
	}

	public function getMedia()
	{
		return $this->_media;
	}

	public function getPriority()
	{
		return $this->_priority;
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