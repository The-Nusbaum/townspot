<?php
namespace Townspot\Rating;

class Entity extends \Townspot\Entity
{
	protected $_id;

	protected $_media;

	protected $_user;

	protected $_rating;

	public function __construct()
	{
	}

	public function setMedia(\Townspot\Media\Entity $value)
	{
		$this->_media = $value;
		return $this;
	}

	public function setUser(\Townspot\User\Entity $value)
	{
		$this->_user = $value;
		return $this;
	}

	public function setRating($value)
	{
		if ($value === true) {
			$this->_rating = 1;
		} elseif ($value === false) {
			$this->_rating = -1;
		}
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function getMedia()
	{
		return $this->_media;
	}

	public function getUser()
	{
		return $this->_user;
	}

	public function getRating()
	{
		if ($this->_rating === 1 ) {
			return true;
		} elseif ($this->_rating === -1) {
			return false;
		}
		return null;
	}
}