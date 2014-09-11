<?php
namespace Townspot\CountryRegion;

class Entity
{
	protected $_id;

	protected $_name;

	protected $_coords;

	protected $_created;

	protected $_updated;

	protected $_provinces;

	protected $_cities;

	protected $_country;

	public function __construct()
	{
		$this->_created = new \DateTime();
		$this->_updated = new \DateTime();
		$this->_provinces = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_cities = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function setName($value)
	{
		$this->_name = $value;
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

	public function setCountry(\Townspot\Country\Entity $value)
	{
		$this->_country = $value;
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

	public function getCountry()
	{
		return $this->_country;
	}
}
