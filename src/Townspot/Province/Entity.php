<?php
namespace Townspot\Province;

class Entity extends \Townspot\Entity
{
	protected $_id;

	protected $_name;

	protected $_us_territory;
	
	protected $_abbrev;
	
	protected $_latitude;
	
	protected $_longitude;
	
	protected $_coords;
	
	protected $_created;
	
	protected $_updated;
	
	protected $_cities;
	
	protected $_country;
	
	protected $_country_region;
	
	protected $_users;
	
	public function __construct()
	{
		$this->_created = new \DateTime();
		$this->_updated = new \DateTime();
		$this->_cities = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_users = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function setName($value)
	{
		$this->_name = $value;
		return $this;
	}

	public function setUsTerritory($value)
	{
		$this->_us_territory = $value;
		return $this;
	}
	
	public function setAbbrev($value)
	{
		$this->_abbrev = $value;
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
	
	public function addCity(\Townspot\City\Entity $value)
	{
		$this->_cities->add($value);
		return $this;
	}
	
	public function removeCity($key)
	{
		$this->_cities->remove($key);
		return $this;
	}
	
	public function setCountry(\Townspot\Country\Entity $value)
	{
		$this->_country = $value;
		return $this;
	}
	
	public function setCountryRegion(\Townspot\CountryRegion\Entity $value)
	{
		$this->_country_region = $value;
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

	public function getName()
	{
		return $this->_name;
	}

	public function getUsTerritory()
	{
		return $this->_us_territory;
	}
	
	public function getAbbrev()
	{
		return $this->_abbrev;
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
	
	public function getCities()
	{
		return $this->_cities;
	}
	
	public function getCountry()
	{
		return $this->_country;
	}
	
	public function getCountryRegion()
	{
		return $this->_country_region;
	}

	public function getUsers()
	{
		return $this->_users;
	}
	
	public function getDiscoverLink()
	{
		return sprintf('/videos/%s',
				htmlentities(strtolower($this->getName()))
		);
	}
	
}
