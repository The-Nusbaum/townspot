<?php
namespace Townspot\Media;

class Entity
{
	protected $_id;

	protected $_title;

	protected $_media_type;

	protected $_source;

	protected $_logline;

	protected $_description;

	protected $_why_we_chose;

	protected $_url;

	protected $_preview_image;

	protected $_views;

	protected $_duration;

	protected $_allow_contact;

	protected $_authorised;

	protected $_request_debut_time;

	protected $_approved;

	protected $_on_media_server;

	protected $_neighborhood;

	protected $_latitude;

	protected $_longitude;

	protected $_user;

	protected $_admin;

	protected $_country;

	protected $_province;

	protected $_city;

	protected $_categories;

	protected $_fans;

	protected $_created;

	protected $_updated;

	protected $_debut_time;

	protected $_encodings;

	protected $_ratings;

	protected $_tags;
	
	protected $_activity;
	
	protected $_comments_about;
	
	protected $_schedule;

	protected $_section_media;
	
	public function __construct()
	{
		$this->_created = new \DateTime();
		$this->_updated = new \DateTime();
		$this->_debut_time = new \DateTime();
		$this->_categories = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_fans = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_encodings = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_ratings = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_tags = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_activity = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_comments_about = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function setTitle($value)
	{
		$this->_title = $value;
		return $this;
	}

	public function setMediaType($value)
	{
		$this->_media_type = $value;
		return $this;
	}

	public function setSource($value)
	{
		$this->_source = $value;
		return $this;
	}

	public function setLogline($value)
	{
		$this->_logline = $value;
		return $this;
	}

	public function setDescription($value)
	{
		$this->_description = $value;
		return $this;
	}

	public function setWhyWeChose($value)
	{
		$this->_why_we_chose = $value;
		return $this;
	}

	public function setUrl($value)
	{
		$uri = Zend\Uri\UriFactory::factory($value);
		if ($uri->isValid()) {
			$this->_url = $value;
		}
		return $this;
	}

	public function setPreviewImage($value)
	{
		$uri = Zend\Uri\UriFactory::factory($value);
		if ($uri->isValid()) {
			$this->_preview_image = $value;
		}
		return $this;
	}

	public function setViews($value)
	{
		$this->_views = $value;
		return $this;
	}

	public function incrViews()
	{
		$views = $this->getViews();
		$views++;
		$this->setViews($views);
		return $this;
	}
	
	public function setDuration($value)
	{
		$this->_duration = $value;
		return $this;
	}

	public function setAllowContact($value)
	{
		$this->_allow_contact = $value;
		return $this;
	}

	public function setAuthorised($value)
	{
		$this->_authorised = $value;
		return $this;
	}

	public function setRequestDebutTime($value)
	{
		$this->_request_debut_time = $value;
		return $this;
	}

	public function setApproved($value)
	{
		$this->_approved = $value;
		return $this;
	}

	public function setOnMediaServer($value)
	{
		$this->_on_media_server = $value;
		return $this;
	}

	public function setNeighborhood($value)
	{
		$this->_neighborhood = $value;
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

	public function setUser(\Townspot\User\Entity $value)
	{
		$this->_user = $value;
		return $this;
	}

	public function setAdmin(\Townspot\User\Entity $value)
	{
		$this->_admin = $value;
		return $this;
	}

	public function setCountry(\Townspot\Country\Entity $value)
	{
		$this->_country = $value;
		return $this;
	}
	
	public function setProvince(\Townspot\Province\Entity $value)
	{
		$this->_province = $value;
		return $this;
	}

	public function setCity(\Townspot\City\Entity $value)
	{
		$this->_city = $value;
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

	public function addFan(\Townspot\Category\Entity $value)
	{
		$this->_fans->add($value);
		return $this;
	}
	
	public function removeFan($key)
	{
		$this->_fans->remove($key);
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

	public function setDebutTime(\DateTime $value)
	{
		$this->_debut_time = $value;
		return $this;
	}

	public function addEncoding(\Townspot\Encoding\Entity $value)
	{
		$this->_encodings->add($value);
		return $this;
	}
	
	public function removeEncoding($key)
	{
		$this->_encodings->remove($key);
		return $this;
	}

	public function addRating(\Townspot\Rating\Entity $value)
	{
		$this->_ratings->add($value);
		return $this;
	}
	
	public function removeRating($key)
	{
		$this->_ratings->remove($key);
		return $this;
	}

	public function addTag(\Townspot\Rating\Entity $value)
	{
		$this->_tags->add($value);
		return $this;
	}
	
	public function removeTag($key)
	{
		$this->_tags->remove($key);
		return $this;
	}

	public function addActivity(\Townspot\UserActivity\Entity $value)
	{
		$this->_activity->add($value);
		return $this;
	}
	
	public function removeActivity($key)
	{
		$this->_activity->remove($key);
		return $this;
	}

	public function addSchedule(\Townspot\MediaSchedule\Entity $value)
	{
		$this->_schedule->add($value);
		return $this;
	}
	
	public function removeSchedule($key)
	{
		$this->_schedule->remove($key);
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function getTitle()
	{
		return $this->_title;
	}

	public function getMediaType()
	{
		return $this->_media_type;
	}

	public function getSource()
	{
		return $this->_source;
	}

	public function getLogline()
	{
		return $this->_logline;
	}

	public function getDescription()
	{
		return $this->_description;
	}

	public function getWhyWeChose()
	{
		return $this->_why_we_chose;
	}

	public function getUrl()
	{
		return $this->_url;
	}

	public function getPreviewImage()
	{
		return $this->_preview_image;
	}

	public function getViews()
	{
		return $this->_views;
	}

	public function getDuration()
	{
		return $this->_duration;
	}

	public function getAllowContact()
	{
		return (bool)$this->_allow_contact;
	}

	public function getAuthorised()
	{
		return (bool)$this->_authorised;
	}

	public function getRequestDebutTime()
	{
		return (bool)$this->_request_debut_time;
	}

	public function getApproved()
	{
		return (bool)$this->_approved;
	}

	public function getOnMediaServer()
	{
		return $this->_on_media_server;
	}

	public function getNeighborhood()
	{
		return $this->_neighborhood;
	}

	public function getLatitude()
	{
		return $this->_latitude;
	}

	public function getLongitude()
	{
		return $this->_longitude;
	}

	public function getUser()
	{
		return $this->_user;
	}

	public function getAdmin()
	{
		return $this->_admin;
	}

	public function getCountry()
	{
		return $this->_country;
	}
	
	public function getProvince()
	{
		return $this->_province;
	}

	public function getCity()
	{
		return $this->_city;
	}

	public function getCategories()
	{
		return $this->_categories;
	}
	
	public function getFans()
	{
		return $this->_fans;
	}

	public function getCreated()
	{
		return $this->_created;
	}

	public function getUpdated()
	{
		return $this->_updated;
	}

	public function getDebutTime()
	{
		return $this->_debut_time;
	}
	
	public function getEncodings()
	{
		return $this->_encodings;
	}

	public function getRatings()
	{
		return $this->_ratings;
	}

	public function getTags()
	{
		return $this->_tags;
	}

	public function getCommentsAbout()
	{
		return $this->_comments_about;
	}
	
	public function getSchedule()
	{
		return $this->_schedule;
	}

	public function getSectionMedia()
	{
		return $this->_section_media;
	}
}