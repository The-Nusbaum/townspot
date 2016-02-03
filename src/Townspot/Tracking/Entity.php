<?php
namespace Townspot\Tracking;

class Entity extends \Townspot\Entity
{
	protected $_id;

	protected $_type;

	protected $_user;

	protected $_value;

	protected $_created;

	public function __construct()
	{
		$this->_created = new \DateTime();
	}

	public function setType($value)
	{
		$this->_type = $value;
		return $this;
	}

	public function setUser($value)
	{
		$this->_user = $value;
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

	public function getId()
	{
		return $this->_id;
	}

	public function geType()
	{
		return $this->_Type;
	}

	public function getValue()
	{
		return $this->_value;
	}

	public function getCreated()
	{
		return $this->_created;
	}

	public function getUser()
	{
		return $this->_user;
	}
}