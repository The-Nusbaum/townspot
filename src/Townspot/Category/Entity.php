<?php
namespace Townspot\Category;

class Entity extends \Townspot\Entity
{
	protected $_id;

	protected $_name;

	protected $_allow_freeform;

	protected $_categories;

	protected $_parent;

	protected $_category_type;

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

	public function setAllowFreeform($value)
	{
		$this->_allow_freeform = $value;
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

	public function setParent(\Townspot\Category\Entity $value)
	{
		$this->_parent = $value;
		return $this;
	}

	public function setCategoryType(\Townspot\CategoryType\Entity $value)
	{
		$this->_category_type = $value;
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

	public function getAllowFreeform()
	{
		return (bool) $this->_allow_freeform;
	}

	public function getCategories()
	{
		return $this->_categories;
	}
	
	public function getParent()
	{
		return $this->_parent;
	}

	public function getCategoryType()
	{
		return $this->_category_type;
	}

	public function getCreated()
	{
		return $this->_created;
	}

	public function getUpdated()
	{
		return $this->_updated;
	}
	
	public function getDiscoverLink()
	{
		$link = '/videos' . $this->getDiscoverLevels();;
		return $link;
	}

	public function getDiscoverLevels()
	{
		$link = '';
		if ($parent = $this->getParent()){
			$link .= $parent->getDiscoverLevels();
		}
		$link .= '/' . htmlentities($this->getName());
		return $link;
	}
	
}