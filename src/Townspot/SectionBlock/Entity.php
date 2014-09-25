<?php
namespace Townspot\SectionBlock;

class Entity
{
	protected $_id;

	protected $_section_media;
	
	protected $_block_name;

	protected $_created;

	protected $_updated;

	public function __construct()
	{
		$this->_created = new \DateTime();
		$this->_updated = new \DateTime();
		$this->_section_media = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function addSectionMedia(\Townspot\SectionMedia\Entity $value)
	{
		$this->_section_media->add($value);
		return $this;
	}

	public function removeSectionMedia($key)
	{
		$this->_section_media->remove($key);
		return $this;
	}
	
	public function setBlockName($value)
	{
		$this->_block_name = $value;
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

	public function getSectionMedia()
	{
		$_media = array();
		$sectionMedia = $this->_section_media;
		foreach ($sectionMedia as $media) {
			$_media[$media->getPriority()] = $media;
		}
		ksort($_media);
		return $_media;
	}

	public function getBlockName()
	{
		return $this->_block_name;
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