<?php
namespace Townspot\UserRole;

class Entity extends \Townspot\Entity
{
	protected $_id;
	
	protected $_is_default;

	protected $_description;

	protected $_roles;

	protected $_parent;

	protected $_users;

	public function __construct()
	{
		$this->_created = new \DateTime();
		$this->_updated = new \DateTime();
		$this->_roles = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_users = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function setIsDefault($value)
	{
		$this->_is_default = $value;
		return $this;
	}

	public function setDescription($value)
	{
		$this->_description = $value;
		return $this;
	}

	public function addRole(\Townspot\UserRole\Entity $value)
	{
		$this->_roles->add($value);
		return $this;
	}
	
	public function removeRole($key)
	{
		$this->_roles->remove($key);
		return $this;
	}

	public function setParent(\Townspot\UserRole\Entity $value)
	{
		$this->_parent = $value;
		return $this;
	}

	public function addUser(\Townspot\User\Entity $value)
	{
		$this->_users->add($value);
		return $this;
	}
	
	public function removeUser($key)
	{
		$this->_users->remove($key);
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}
	
	public function getIsDefault()
	{
		return $this->_is_default;
	}

	public function getDescription()
	{
		return $this->_description;
	}

	public function getRoles()
	{
		return $this->_roles;
	}
	
	public function getParent()
	{
		return $this->_parent;
	}

	public function getUsers()
	{
		return $this->_users;
	}
}