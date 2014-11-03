<?php
namespace Townspot\City;

class Entity extends \Townspot\Entity
{
	protected $_id;

	protected $_name;
	
	protected $_timezone;
	
	protected $_latitude;
	
	protected $_longitude;
	
	protected $_coords;
	
	protected $_created;
	
	protected $_updated;
	
	protected $_neighborhoods;
	
	protected $_country;
	
	protected $_country_region;
	
	protected $_province;
	
	protected $_users;
	
	protected $_media;

	public function __construct()
	{
		$this->_created = new \DateTime();
		$this->_updated = new \DateTime();
		$this->_neighborhoods = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_users = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_media = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function setName($value)
	{
		$this->_name = $value;
		return $this;
	}

	public function setTimezone($value)
	{
		$this->_timezone = $value;
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
	
	public function addNeighborhood(\Townspot\Neighborhood\Entity $value)
	{
		$this->_neighborhoods->add($value);
		return $this;
	}
	
	public function removeNeighborhood($key)
	{
		$this->_neighborhoods->remove($key);
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

	public function setProvince(\Townspot\Province\Entity $value)
	{
		$this->_province = $value;
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

	public function addMedia(\Townspot\Media\Entity $value)
	{
		$this->_media->add($value);
		return $this;
	}

	public function removeMedia($key)
	{
		$this->_media->remove($key);
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
	
	public function getTimezone()
	{
		return $this->_timezone;
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
	
	public function getNeighborhoods()
	{
		return $this->_neighborhoods;
	}
	
	public function getCountry()
	{
		return $this->_country;
	}
	
	public function getCountryRegion()
	{
		return $this->_country_region;
	}

	public function getProvince()
	{
		return $this->_province;
	}

	public function getUsers()
	{
		return $this->_users;
	}
	
	public function getMedia()
	{
		return $this->_media;
	}

	public function getFullName()
	{
		$abbrev = null;
		if ($province = $this->getProvince()) {
			$abbrev = $province->getAbbrev();
		}
		if ($abbrev) {
			return $this->getName() . ', ' . $abbrev;
		}
		return $this->getName();
	}

	public function getDiscoverLink()
	{
		return sprintf('/discover/%s/%s',
				htmlentities(strtolower($this->getProvince()->getName())),
				htmlentities(strtolower($this->getName()))
		);
	}
	
	public function getRandomMedia()
	{
		$media = $this->getMedia();
		$randkey = rand(0,count($media));
		return $media[$randkey];
	}
}
