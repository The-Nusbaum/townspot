<?php
namespace Townspot\SeriesSeason;

class Entity
{
	protected $_id;

	protected $_name;

	protected $_description;

	protected $_season_number;
	
	protected $_series;

	protected $_episodes;
	
	protected $_created;

	protected $_updated;

	public function __construct()
	{
		$this->_created = new \DateTime();
		$this->_updated = new \DateTime();
		$this->_episodes = new \Doctrine\Common\Collections\ArrayCollection();
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

	public function setSeasonNumber($value)
	{
		$this->_season_number = $value;
		return $this;
	}

	public function setSeries(\Townspot\Series\Entity $value)
	{
		$this->_series = $value;
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

	public function getSeasonNumber()
	{
		return $this->_season_number;
	}

	public function getSeries()
	{
		return $this->_series;
	}

	public function getEpisodes()
	{
		return $this->_episodes;
	}
	
	public function getCreated()
	{
		return $this->_created;
	}

	public function getUpdated()
	{
		return $this->_updated;
	}
}