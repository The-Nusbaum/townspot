<?php
namespace Townspot\Neighborhood;

class Entity extends \Townspot\Entity
{
	protected $_id;

	protected $_name;
	
	protected $_latitude;
	
	protected $_longitude;
	
	protected $_coords;
	
	protected $_created;
	
	protected $_updated;
	
	protected $_city;

	public function __construct()
	{
		$this->_created = new \DateTime();
		$this->_updated = new \DateTime();
	}

	public function setName($value)
	{
		$this->_name = $value;
		return $this;
	}
	
	public function setLatitude($value)
	{
		$this->_latitude = $value;
		return $this;
	}

	public function setLongitude($value)
	{
		$this->_longitude = $value;
		return $this;
	}
	
	public function setCoords($value)
	{
		$this->_coords = $value;
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
	
	public function setCity(\Townspot\City\Entity $value)
	{
		$this->_city = $value;
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
	
	public function getLatitude()
	{
		return $this->_latitude;
	}

	public function getLongitude()
	{
		return $this->_longitude;
	}
	
	public function getCoords()
	{
		return $this->_coords;
	}
	
	public function getCreated()
	{
		return $this->_created;
	}

	public function getUpdated()
	{
		return $this->_updated;
	}
	
	public function getCity()
	{
		return $this->_city;
	}
}
