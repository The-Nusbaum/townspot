<?php
namespace Townspot\User;

class Entity extends \Townspot\Entity
{
	protected $_id;

	protected $_username;

	protected $_password;

	protected $_email;

	protected $_first_name;

	protected $_last_name;

	protected $_display_name;

	protected $_activation_string;

	protected $_security_key;

	protected $_neighborhood;

	protected $_about_me;

	protected $_interests;

	protected $_description;

	protected $_website;

	protected $_image_url;

	protected $_upload_url;

	protected $_artist_name;

	protected $_state;

	protected $_latitude;

	protected $_longitude;

	protected $_allow_contact;

	protected $_terms_agreement;

	protected $_email_notifications;

	protected $_created;

	protected $_updated;

	protected $_country;

	protected $_province;

	protected $_city;

	protected $_roles;

	protected $_user_oauth;

	protected $_user_social;

	protected $_following;

	protected $_followed_by;

	protected $_user_events;

	protected $_media;
	
	protected $_series;
	
	protected $_approved;
	
	protected $_favorites;

	protected $_ratings;

	protected $_activity;
	
	protected $_activities_against;
	
	protected $_comments;

	protected $_comments_about;
	
	protected $_media_comments;
	
	public function __construct()
	{
		$this->_created = new \DateTime();
		$this->_updated = new \DateTime();
		$this->_roles = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_user_oauth = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_user_social = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_following = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_followed_by = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_user_events = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_ratings = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_activity = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_activities_against = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_comments = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_comments_about = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_media_comments = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function setUsername($value)
	{
		$this->_username = $value;
		return $this;
	}

	public function setPassword($value)
	{
		$this->_password = $value;
		return $this;
	}

	public function setEmail($value)
	{
		$this->_email = $value;
		return $this;
	}

	public function setFirstName($value)
	{
		$this->_first_name = $value;
		return $this;
	}

	public function setLastName($value)
	{
		$this->_last_name = $value;
		return $this;
	}
	
	public function setDisplayName($value)
	{
		$this->_display_name = $value;
		return $this;
	}

	public function setActivationString($value)
	{
		$this->_activation_string = $value;
		return $this;
	}

	public function generateActivationString()
	{
		return $this->setActivationString(md5(uniqid() . uniqid()));
	}

	public function setSecurityKey($value)
	{
		$this->_security_key = $value;
		return $this;
	}

	public function generateSecurityKey()
	{
		return $this->setSecurityKey(md5(uniqid() . uniqid()));
	}

	public function setNeighborhood($value)
	{
		$this->_neighborhood = $value;
		return $this;
	}

	public function setAboutMe($value)
	{
		$this->_about_me = $value;
		return $this;
	}

	public function setInterests($value)
	{
		$this->_interests = $value;
		return $this;
	}

	public function setDescriptions($value)
	{
		$this->_description = $value;
		return $this;
	}

	public function setWebsite($value)
	{
		$uri = Zend\Uri\UriFactory::factory($value);
		if ($uri->isValid()) {
			$this->_website = $value;
		}
		return $this;
	}

	public function setImageUrl($value)
	{
		$uri = Zend\Uri\UriFactory::factory($value);
		if ($uri->isValid()) {
			$this->_image_url = $value;
		}
		return $this;
	}

	public function setUploadUrl($value)
	{
		$uri = Zend\Uri\UriFactory::factory($value);
		if ($uri->isValid()) {
			$this->_upload_url = $value;
		}
		return $this;
	}

	public function setArtistName($value)
	{
		$this->_artist_name = $value;
		return $this;
	}

	public function setState($value)
	{
		$this->_state = $value;
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

	public function setAllowContact($value)
	{
		$this->_allow_contact = $value;
		return $this;
	}

	public function setTermsAgreement($value)
	{
		$this->_terms_agreement = $value;
		return $this;
	}

	public function setEmailNotification($value)
	{
		$this->_email_notifications = $value;
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

	public function addRole(\Townspot\UserRole\Entity $value)
	{
		$this->_roles->add($value);
		return $this;
	}
	
	public function removeRole($key)
	{
		$this->_roles->remove($key);
		return $this;
	}
	
	public function addOauth(\Townspot\UserOauth\Entity $value)
	{
		$this->_user_oauth->add($value);
		return $this;
	}
	
	public function removeOauth($key)
	{
		$this->_user_oauth->remove($key);
		return $this;
	}

	public function addSocialMedia(\Townspot\UserSocialMedia\Entity $value)
	{
		$this->_user_social->add($value);
		return $this;
	}
	
	public function removeSocialMedia($key)
	{
		$this->_user_social->remove($key);
		return $this;
	}

	public function addFollowing(\Townspot\User\Entity $value)
	{
		$this->_following->add($value);
		return $this;
	}
	
	public function removeFollowing($key)
	{
		$this->_following->remove($key);
		return $this;
	}

	public function addFollower(\Townspot\User\Entity $value)
	{
		$this->_followed_by->add($value);
		return $this;
	}
	
	public function removeFollower($key)
	{
		$this->_followed_by->remove($key);
		return $this;
	}

	public function addEvent(\Townspot\UserEvent\Entity $value)
	{
		$this->_user_events->add($value);
		return $this;
	}
	
	public function removeEvent($key)
	{
		$this->_user_events->remove($key);
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

	public function addSeries(\Townspot\Series\Entity $value)
	{
		$this->_series->add($value);
		return $this;
	}
	
	public function removeSeries($key)
	{
		$this->_series->remove($key);
		return $this;
	}

	public function addApproval(\Townspot\Media\Entity $value)
	{
		$this->_approved->add($value);
		return $this;
	}
	
	public function removeApproval($key)
	{
		$this->_approved->remove($key);
		return $this;
	}
	
	public function addFavorite(\Townspot\Media\Entity $value)
	{
		$this->_favorites->add($value);
		return $this;
	}
	
	public function removeFavorite($key)
	{
		$this->_favorites->remove($key);
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

	public function addActivityAgainst(\Townspot\UserActivity\Entity $value)
	{
		$this->_activities_against->add($value);
		return $this;
	}
	
	public function removeActivityAgainst($key)
	{
		$this->_activities_against->remove($key);
		return $this;
	}

	public function addComment(\Townspot\ArtistComment\Entity $value)
	{
		$this->_comments->add($value);
		return $this;
	}
	
	public function removeComment($key)
	{
		$this->_comments->remove($key);
		return $this;
	}

	public function addMediaComment(\Townspot\MediaComment\Entity $value)
	{
		$this->_media_comments->add($value);
		return $this;
	}
	
	public function removeMediaComment($key)
	{
		$this->_media_comments->remove($key);
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function getUsername($escaped = false)
	{
		if ($escaped) {
			return htmlentities($this->_username);
		}
		return $this->_username;
	}

	public function getPassword()
	{
		return $this->_password;
	}

	public function getEmail()
	{
		return $this->_email;
	}

	public function getFirstName()
	{
		return $this->_first_name;
	}
	
	public function getLastName()
	{
		return $this->_last_name;
	}
	
	public function getDisplayName()
	{
		return $this->_display_name;
	}

	public function getActivationString()
	{
		return $this->_activation_string;
	}

	public function getSecurityKey()
	{
		return $this->_security_key;
	}

	public function getNeighborhood()
	{
		return $this->_neighborhood;
	}

	public function getAboutMe()
	{
		return $this->_about_me;
	}

	public function getInterests()
	{
		return $this->_interests;
	}

	public function getDescriptions()
	{
		return $this->_description;
	}

	public function getWebsite()
	{
		return $this->_website;
	}

	public function getImageUrl()
	{
		return $this->_image_url;
	}

	public function getUploadUrl()
	{
		return $this->_upload_url;
	}

	public function getArtistName()
	{
		return $this->_artist_name;
	}

	public function getState()
	{
		return $this->_state;
	}

	public function getLatitude()
	{
		return $this->_latitude;
	}

	public function getLongitude()
	{
		return $this->_longitude;
	}

	public function getAllowContact()
	{
		return (bool)$this->_allow_contact;
	}

	public function getTermsAgreement()
	{
		return (bool)$this->_terms_agreement;
	}

	public function getEmailNotification()
	{
		return (bool)$this->_email_notifications;
	}

	public function getCreated()
	{
		return $this->_created;
	}

	public function getUpdated()
	{
		return $this->_updated;
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

	public function getRoles()
	{
		return $this->_roles;
	}
	
	public function getUserOauth()
	{
		return $this->_user_oauth;
	}
	
	public function getUserSocial()
	{
		return $this->_user_social;
	}

	public function getFollowing()
	{
		return $this->_following;
	}

	public function getFollowedBy()
	{
		return $this->_followed_by;
	}

	public function getUserEvents()
	{
		return $this->_user_events;
	}

	public function getMedia()
	{
		return $this->_media;
	}
	
	public function getApproved()
	{
		return $this->_approved;
	}
	
	public function getFavorites()
	{
		return $this->_favorites;
	}
	
	public function getRatings()
	{
		return $this->_ratings;
	}
	
	public function getComments()
	{
		return $this->_comments;
	}

	public function getMediaComments()
	{
		return $this->_media_comments;
	}

	public function getCommentsAbout()
	{
		return $this->_comments_about;
	}
	
	public function getProfileLink()
	{
		return "/profile/" . $this->getUsername();
	}
	
	public function getProfileImage($width=100,$height=100)
	{
		return sprintf('/resizer.php?id=%d&type=profile&w=%d&h=%d',
			$this->getId(),
			$width,
			$height);
	}
	
	public function getRandomMedia()
	{
		if ($media = $this->getMedia()) {
			$randkey = rand(0,(count($media)-1));
			return $media[$randkey];
		}
		return null;
	}

    public function getSeries() {
        return $this->_series;
    }

    public function setSeries($value) {
        $this->_series = $value;
        return $this;
    }
}