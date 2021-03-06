<?php
namespace Townspot\Media;

require_once APPLICATION_PATH . "/google/apiclient/src/Google/autoload.php";

/**
 * Class Entity
 * @package Townspot\Media
 */
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

    protected $_api_info = array(
        'applicationId' => 'Townspot',
        'clientId' => "872367745273-sbsiuc81kh9o70ok3macc15d2ebpl440.apps.googleusercontent.com",
        'developerId' => "AIzaSyCa1RYJsf-C94cTQo34GC59DkiijUq_54s",
        //'developerId' => "KkR-tZy_lJmcHHzlKtFWDAdD",
        //'developerId' => "AI39si7sp4rb57_29xMFWO2AoT8DDc0dKYklw7_IsUYpEhsxSL-DNK60f3eIF7OK_Iy0_xYm1eAxX2skGO57B6oHd6qJHHiPZA"
    );
	
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

	public function addFan(\Townspot\User\Entity $value)
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

	/**
	 * @return mixed
     */
	public function getId()
	{
		return $this->_id;
	}

	/**
	 * @param bool $formatted
	 * @param bool $escaped
	 * @return mixed|string
     */
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

	/**
	 * @return mixed
     */
	public function getMediaType()
	{
		return $this->_media_type;
	}

	/**
	 * @return mixed
     */
	public function getSource()
	{
		return $this->_source;
	}

	/**
	 * @param bool $escaped
	 * @return string
     */
	public function getLogline($escaped = false)
	{
		$logline = $this->_logline;
		if ($escaped) {
			$logline = htmlentities($logline);
		}
		return $logline;
	}

	/**
	 * @param bool $escaped
	 * @return mixed|string
     */
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

	/**
	 * @param bool $escaped
	 * @return mixed|string
     */
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

	/**
	 * @return mixed
     */
	public function getUrl()
	{
		return $this->_url;
	}

	/**
	 * @return mixed
     */
	public function getPreviewImage()
	{
		return $this->_preview_image;
	}

	/**
	 * @param bool $fromSource
	 * @return mixed
     */
	public function getViews($fromSource = true)
	{
		if ($fromSource) {
			if ($this->getSource() == 'youtube') {
				$ytId = $this->getYtVideoId();
				$videoEntry = $this->_getYtVideo($ytId);
				if($videoEntry) return $videoEntry->getStatistics()->getViewCount();
			}
		}
		return $this->_views;
	}

	/**
	 * @param bool $formatted
	 * @param bool $fromSource
	 * @return int|string
     */
	public function getDuration($formatted = false,$fromSource = false)
	{
		$duration = $this->_duration;
		if ($fromSource) {
			if ($this->getSource() == 'youtube') {
				$ytId = $this->getYtVideoId();
				$videoEntry = $this->_getYtVideo($ytId);
				if(!$videoEntry) return $duration;
				$duration = $videoEntry->getContentDetails()->getDuration();
                preg_match('/([0-9]*)H/',$hours);
                preg_match('/([0-9]*)M/',$minutes);
                preg_match('/([0-9]*)S/',$seconds);

                $durationInSecs = 0;
                if(!empty($hours[1])) $durationInSecs += $hours[1] * 60 * 60;
                if(!empty($minutes[1])) $durationInSecs += $minutes[1] * 60;
                if(!empty($seconds[1])) $durationInSecs += $seconds[1];

                $duration = $durationInSecs;
			}
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

	/**
	 * @return bool
     */
	public function getAllowContact()
	{
		return (bool)$this->_allow_contact;
	}

	/**
	 * @return bool
     */
	public function getAuthorised()
	{
		return (bool)$this->_authorised;
	}

	/**
	 * @return bool
     */
	public function getRequestDebutTime()
	{
		return (bool)$this->_request_debut_time;
	}

	/**
	 * @return bool
     */
	public function getApproved()
	{
		return (bool)$this->_approved;
	}

	/**
	 * @return mixed
     */
	public function getOnMediaServer()
	{
		return $this->_on_media_server;
	}

	/**
	 * @return mixed
     */
	public function getNeighborhood()
	{
		return $this->_neighborhood;
	}

	/**
	 * @return mixed
     */
	public function getLatitude()
	{
		return $this->_latitude;
	}

	/**
	 * @return mixed
     */
	public function getLongitude()
	{
		return $this->_longitude;
	}

	/**
	 * @return \Townspot\User\Entity
     */
	public function getUser()
	{
		return $this->_user;
	}

	/**
	 * @return mixed
     */
	public function getAdmin()
	{
		return $this->_admin;
	}

	/**
	 * @return mixed
     */
	public function getCountry()
	{
		return $this->_country;
	}

	/**
	 * @return mixed
     */
	public function getProvince()
	{
		return $this->_province;
	}

	/**
	 * @return mixed
     */
	public function getCity()
	{
		return $this->_city;
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
     */
	public function getCategories()
	{
		return $this->_categories;
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
     */
	public function getFans()
	{
		return $this->_fans;
	}

	/**
	 * @return \DateTime
     */
	public function getCreated()
	{
		return $this->_created;
	}

	/**
	 * @return \DateTime
     */
	public function getUpdated()
	{
		return $this->_updated;
	}

	/**
	 * @return \DateTime
     */
	public function getDebutTime()
	{
		return $this->_debut_time;
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
     */
	public function getEncodings()
	{
		return $this->_encodings;
	}

	/**
	 * @param null $rate
	 * @return int|array|\Doctrine\Common\Collections\ArrayCollection
     */
	public function getRatings($rate = null, $count = false)
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
		if ($count) return count($ratings);
		return $ratings;
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
     */
	public function getTags()
	{
		return $this->_tags;
	}

	/**
	 * @param string $order
	 * @return int|array|\Doctrine\Common\Collections\ArrayCollection
     */
	public function getCommentsAbout($order = 'ASC', $count = false)
	{
		if($count) return count($this->_comments_about);
		if ($order == 'ASC') {
			return array_reverse($this->_comments_about->toArray());
		} 
		return $this->_comments_about;
	}

	/**
	 * @return mixed
     */
	public function getSchedule()
	{
		return $this->_schedule;
	}

	/**
	 * @return mixed
     */
	public function getSectionMedia()
	{
		return $this->_section_media;
	}

	/**
	 * @return mixed
     */
	public function getEpisode()
    {
        return $this->_episode;
    }

	/**
	 * @param string $resolution
	 * @return mixed|string
     */
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

	/**
	 * @return string
     */
	public function getMediaLink()
	{
		return sprintf('/videos/%d/%s',
			$this->getId(),
			strtolower($this->getTitle(true))
		);
	}

	/**
	 * @return string
     */
	public function getEmbedLink()
	{
		return sprintf('/embed/%d/%s',
			$this->getId(),
			$this->getTitle(true)
		);
	}

	/**
	 * @param int $width
	 * @param int $height
	 * @return mixed|string
     */
	public function getResizerLink($width = 332,$height = 249)
	{
		if (!(preg_match('/ytimg|vimeocdn|dmcdn/',$this->getPreviewImage()))) {
			return sprintf('/resizer.php?id=%d&w=%d&h=%d',
				$this->getId(),
				$width,
				$height);
		}
		return $this->getPreviewImage();
	}

	/**
	 * @param int $width
	 * @param int $height
	 * @return mixed|string
     */
	public function getResizerCdnLink($width = 332,$height = 249)
	{
		$imageServer = "http://images" . rand(0,9) . ".townspot.tv";
		$link = $this->getResizerLink($width,$height);
		
		if (preg_match('/^http/',$link)) {
			return $link;
		}
		return $imageServer . $link;
	}

	/**
	 * @param bool $includeNeighborhood
	 * @param bool $escaped
	 * @return string
     */
	public function getLocation($includeNeighborhood = false,$escaped = false)
	{
		/*
		$location = sprintf("%s, %s, %s",
			$this->getCity()->getName(),
			$this->getProvince()->getName(),
			$this->getCountry()->getName()
		);
		*/
		$location = sprintf("%s, %s",
			$this->getCity()->getName(),
			$this->getCountry()->getName()
		);
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

	/**
	 * @return mixed
     */
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

	/**
	 * @return null
     */
	public function getYtAuthor()
    {
        if ($this->getSource() == 'youtube') {
            $ytId = $this->getYtVideoId();
            $videoEntry = $this->_getYtVideo($ytId);
	    if(!$videoEntry) return null;
            $author = $videoEntry->getSnippet()->getChannelTitle();
            return $author;
        }
        return null;
    }

	/**
	 * @return null
     */
	public function getYtSubscriberChannelId()
	{
		if ($this->getSource() == 'youtube') {
			$ytId = $this->getYtVideoId();
			$videoEntry = $this->_getYtVideo($ytId);
			if(!$videoEntry) return null;
            $author = $videoEntry->getSnippet()->getChannelId();
            return $author;
		}
		return null;
	}

	/**
	 * @return null
     */
	public function getYtSubscriberChannelTitle()
    {
        if ($this->getSource() == 'youtube') {
            $ytId = $this->getYtVideoId();
            $videoEntry = $this->_getYtVideo($ytId);
	    if(!$videoEntry) return null;
            $author = $videoEntry->getSnippet()->getChannelTitle();
            return $author;
        }
        return null;
    }

/*
	protected function _getYtApi()
	{
		$yt = new \ZendGData\YouTube();			
		$yt->getHttpClient()->setOptions(array('sslverifypeer' => false));
		return $yt;
	}
*/

    protected function _getYtApi()
    {
        $client = new \Google_Client();
        $client->setApplicationName($this->_api_info['applicationId']);
        $client->setDeveloperKey($this->_api_info['developerId']);
        $api = new \Google_Service_YouTube($client);
        return $api->videos;
    }

	protected function _getYtVideo($id)
	{
		$video = $this->_getYtApi()->listVideos("snippet,contentDetails,statistics",
            array('id' => $id));
        return $video->getItems()[0];
	}
	
}
