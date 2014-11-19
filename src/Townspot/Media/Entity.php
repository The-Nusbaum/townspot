<?php
namespace Townspot\Media;

class Entity extends \Townspot\Entity
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
	
	protected $_episode;
	
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
		$uri = \Zend\Uri\UriFactory::factory($value);
		if ($uri->isValid()) {
			$this->_url = $value;
		}
		return $this;
	}

	public function setPreviewImage($value)
	{
		$uri = \Zend\Uri\UriFactory::factory($value);
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

	public function getTitle($formatted = false,$escaped = false)
	{
		$title = $this->_title;
		if ($escaped) {
			$title = htmlentities($title);
		}
		if (!$formatted) {
			return $title;
		}
		$title = preg_replace('/[^A-Za-z0-9- ]/', '', $title);
		return str_replace(' ','_',$title);
	}

	public function getMediaType()
	{
		return $this->_media_type;
	}

	public function getSource()
	{
		return $this->_source;
	}

	public function getLogline($escaped = false)
	{
		$logline = $this->_logline;
		if ($escaped) {
			$logline = htmlentities($logline);
		}
		return $logline;
	}

	public function getDescription($escaped = false)
	{
		$description = $this->_description;
		if ($escaped) {
			$description = preg_replace('/<br\s*\/?\s*>/', "\n", $description);
			$description = strip_tags($description);
			$description = htmlentities($description);
			$description = nl2br($description);
		}
		return $description;
	}

	public function getWhyWeChose($escaped = false)
	{
		$why_we_chose = $this->_why_we_chose;
		if ($escaped) {
			$why_we_chose = preg_replace('/<br\s*\/?\s*>/', "\n", $why_we_chose);
			$why_we_chose = strip_tags($why_we_chose);
			$why_we_chose = htmlentities($why_we_chose);
			$why_we_chose = nl2br($why_we_chose);
		}
		return $why_we_chose;
	}

	public function getUrl()
	{
		return $this->_url;
	}

	public function getPreviewImage()
	{
		return $this->_preview_image;
	}

	public function getViews($fromSource = true)
	{
		if ($fromSource) {
			if ($this->getSource() == 'youtube') {
				$ytId = $this->getYtVideoId();
				$videoEntry = $this->_getYtVideo($ytId);
				return $videoEntry->getStatistics()->getViewCount();
			}
		}
		return $this->_views;
	}

	public function getDuration($formatted = false)
	{
		$duration = $this->_duration;
		if ($this->getSource() == 'youtube') {
			$ytId = $this->getYtVideoId();
			$videoEntry = $this->_getYtVideo($ytId);
			$duration = $videoEntry->getMediaGroup()->getDuration()->getSeconds();
		}
		if ($formatted) {
			$d1 = new \DateTime(); 	
			$d2 = new \DateTime();
			$d2->add(new \DateInterval('PT'.intval($duration).'S'));
			$interval = $d2->diff($d1);
			return $interval->format("%H:%I:%S");
		}
		return $duration;
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

	public function getRatings($rate = null)
	{
		if ($rate === null) {
			return $this->_ratings;
		}
		$ratings = array();
		foreach ($this->_ratings as $rating) {
			if ($rating->getRating() === $rate) {
				$ratings[] = $rating;
			}
		}
		return $ratings;
	}

	public function getTags()
	{
		return $this->_tags;
	}

	public function getCommentsAbout($order = 'ASC')
	{
		if ($order == 'ASC') {
			return array_reverse($this->_comments_about->toArray());
		} 
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
	
	public function getEpisode()
	{
		return $this->_episode;
	}

	public function getMediaUrl($resolution = 'HD')
	{
		switch ($this->getSource()) {
			case 'internal':
				switch ($resolution) {
					case 'HD':
						return 'http://videos.townspot.tv/' . $this->getId() . '_high.mp4';
						break;
					case 'Mobile':
						return 'http://videos.townspot.tv/' . $this->getId() . '_mobile.mp4';
						break;
					default:
						return 'http://videos.townspot.tv/' . $this->getId() . '_standard.mp4';
						break;
				}
				break;
			case 'youtube':
				switch ($resolution) {
					case 'HD':
						return $this->getUrl();
						break;
					case 'Mobile':
						return $this->getUrl();
						break;
					default:
						return $this->getUrl();
						break;
				}
				break;
		}
	}
	
	public function getMediaLink()
	{
		return sprintf('/videos/%d/%s',
			$this->getId(),
			strtolower($this->getTitle(true))
		);
	}
	
	public function getEmbedLink()
	{
		return sprintf('/embed/%d/%s',
			$this->getId(),
			$this->getTitle(true)
		);
	}
	
	public function getResizerLink($width = 332,$height = 249)
	{
		if (!(preg_match('/ytimg/',$this->getPreviewImage()))) {
			return sprintf('/resizer.php?id=%d&w=%d&h=%d',
				$this->getId(),
				$width,
				$height);
		}
		return $this->getPreviewImage();
	}
	
	public function getResizerCdnLink($width = 332,$height = 249)
	{
		$imageServer = "http://images" . rand(0,9) . ".townspot.tv";
		$link = $this->getResizerLink($width,$height);
		
		if (preg_match('/^http/',$link)) {
			return $link;
		}
		return $imageServer . $link;
	}
	
	public function getLocation($includeNeighborhood = false,$escaped = false)
	{
		$location = sprintf("%s, %s",
			$this->getCity()->getName(),
			$this->getProvince()->getAbbrev());
		if ($includeNeighborhood) {
			if ($neighborhood = $this->getNeighborhood()) {
				$location .= sprintf(" (%s)",$neighborhood);
			}
		}
		if ($escaped) {
			$location = htmlentities($location);
		}
		return $location;
	}
	
	public function getYtVideoId()
	{
		$url = $this->getUrl();
		parse_str($url,$results);
		foreach ($results as $key => $value) {
			if (preg_match('/youtube/',$key)) {
				return $value;
			} elseif (preg_match('/youtu/',$key)) {
				$parts = explode('/',$key);
				$id = array_pop($parts);
				return $id;
			}
		}
	}
	
	public function getYtSubscriberChannelId()
	{
		if ($this->getSource() == 'youtube') {
			$ytId = $this->getYtVideoId();
			$videoEntry = $this->_getYtVideo($ytId);
			$authors = $videoEntry->getAuthor();
			$author = array_shift($authors);
			$uri = $author->getUri()->getText();
			$parts = explode('/',$uri);
			$user = array_pop($parts);
			$links = $this->_getYtApi()->getUserProfile($user)->getLink();
			foreach ($links as $link) {
				if (preg_match('/\/channel\//',$link->getHref())) {
					$parts = explode('/',$link->getHref());
					return array_pop($parts);
				}
			}
		}
		return null;
	}

	protected function _getYtApi()
	{
		$yt = new \ZendGData\YouTube();			
		$yt->getHttpClient()->setOptions(array('sslverifypeer' => false));
		return $yt;
	}
	
	protected function _getYtVideo($id)
	{
		return $this->_getYtApi()->getVideoEntry($id);
	}
	
}