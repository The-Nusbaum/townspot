<?php
namespace Townspot\UserOauth;

class Entity extends \Townspot\Entity
{
	protected $_id;

	protected $_source;

	protected $_external_id;

	protected $_user;

	public function __construct()
	{
	}

	public function setSource($value)
	{
		$this->_source = $value;
		return $this;
	}

	public function setExternalId($value)
	{
		$this->_external_id = $value;
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

	public function getSource()
	{
		return $this->_source;
	}

	public function getExternalId()
	{
		return $this->_external_id;
	}

	public function getUser()
	{
		return $this->_user;
	}
}