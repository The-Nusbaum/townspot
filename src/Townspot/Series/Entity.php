<?php
namespace Townspot\Series;

class Entity extends \Townspot\Entity
{
	protected $_id;

	protected $_name;

	protected $_description;
	
	protected $_media_type = 'video';

	protected $_seasons;

	protected $_episodes;

	protected $_user;

	protected $_categories;
	
	protected $_created;
	
	protected $_updated;

	public function __construct()
	{
		$this->_created = new \DateTime();
		$this->_updated = new \DateTime();
		$this->_seasons = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_episodes = new \Doctrine\Common\Collections\ArrayCollection();
		$this->_categories = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function setName($value)
	{
		$this->_name = $value;
		return $this;
	}

	public function setDescription($value)
	{
		$this->_description = $value;
		return $this;
	}

	public function setMediaType($value)
	{
		$this->_media_type = $value;
		return $this;
	}

	public function addSeason(\Townspot\SeriesSeason\Entity $value)
	{
		$this->_seasons->add($value);
		return $this;
	}
	
	public function removeSeason($key)
	{
		$this->_seasons->remove($key);
		return $this;
	}

	public function addEpisode(\Townspot\SeriesEpisode\Entity $value)
	{
		$this->_episodes->add($value);
		return $this;
	}
	
	public function removeEpisode($key)
	{
		$this->_episodes->remove($key);
		return $this;
	}
	
	public function setUser(\Townspot\User\Entity $value)
	{
		$this->_user = $value;
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

	public function getId()
	{
		return $this->_id;
	}

	public function getName()
	{
		return $this->_name;
	}

	public function getDescription()
	{
		return $this->_description;
	}

	public function getMediaType()
	{
		return $this->_media_type;
	}

	public function getSeasons()
	{
		return $this->_seasons;
	}
	
	public function getEpisodes()
	{
		return $this->_episodes;
	}

	public function getUser()
	{
		return $this->_user;
	}

	public function getCategories()
	{
		return $this->_categories;
	}
	
	public function getCreated()
	{
		return $this->_created;
	}

	public function getUpdated()
	{
		return $this->_updated;
	}

	public function getSeriesLink()
	{
		return "/series/" . htmlentities(strtolower($this->getName()));
	}
	
	public function getRandomMedia()
	{
		$episode = $this->getEpisodes();
		if (count($episode)) {
			return $episode[0]->getMedia();
		}
	}
	
}