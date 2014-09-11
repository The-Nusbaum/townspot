<?php
namespace Townspot\Country;

class Entity
{
	protected $_id;

	protected $_name;

	protected $_code2;

	protected $_code3;

	protected $_postal_code_format;

	protected $_coords;

	protected $_created;

	protected $_updated;

	protected $_provinces;

	protected $_cities;

	protected $_country_regions;
	
	protected $_users;

	public function __construct()
	{
		$this->_created = new \DateTime();
		$this->_updated = new \DateTime();
		$this->_provinces = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_cities = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_country_regions = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_users = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function setName($value)
	{
		$this->_name = $value;
		return $this;
	}

	public function setCode2($value)
	{
		$this->_code2 = $value;
		return $this;
	}

	public function setCode3($value)
	{
		$this->_code3 = $value;
		return $this;
	}

	public function setPostalCodeFormat($value)
	{
		$this->_postal_code_format = $value;
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

	public function addProvince(\Townspot\Province\Entity $value)
	{
		$this->_provinces->add($value);
		return $this;
	}
	
	public function removeProvince($key)
	{
		$this->_provinces->remove($key);
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

	public function addCountryRegion(\Townspot\CountryRegion\Entity $value)
	{
		$this->_country_regions->add($value);
		return $this;
	}
	
	public function removeCountryRegion($key)
	{
		$this->_country_regions->remove($key);
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

	public function getCode2()
	{
		return $this->_code2;
	}

	public function getCode3()
	{
		return $this->_code3;
	}

	public function getPostalCodeFormat()
	{
		return $this->_postal_code_format;
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

	public function getProvinces()
	{
		return $this->_provinces;
	}
	
	public function getCities()
	{
		return $this->_cities;
	}
	
	public function getCountryRegions()
	{
		return $this->_country_regions;
	}

	public function getUsers()
	{
		return $this->_users;
	}
}
