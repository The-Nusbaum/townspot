<?php
namespace Townspot\UserSocialMedia;

class Entity extends \Townspot\Entity
{
	protected $_id;

	protected $_source;

	protected $_link;

	protected $_user;

	public function __construct()
	{
	}

	public function setSource($value)
	{
		$this->_source = $value;
		return $this;
	}

	public function setLink($value)
	{
		$this->_link = $value;
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

	public function getLink()
	{
		return $this->_link;
	}

	public function getUser()
	{
		return $this->_user;
	}
}