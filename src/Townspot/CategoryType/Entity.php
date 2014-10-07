<?php
namespace Townspot\CategoryType;

class Entity extends \Townspot\Entity
{
	protected $_id;

	protected $_name;

	protected $_categories;

	protected $_created;

	protected $_updated;

	public function __construct()
	{
		$this->_created = new \DateTime();
		$this->_updated = new \DateTime();
		$this->_categories = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function setName($value)
	{
		$this->_name = $value;
		return $this;
	}

	public function addCategory(\Townspot\Category\Entity $value)
	{
		$this->_categories->add($value);
		return $this;
	}
	
	public function removeCategory($key)
	{
		$this->_categories->remove($key);
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

	public function getCategories()
	{
		return $this->_categories;
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
