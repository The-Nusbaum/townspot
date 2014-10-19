<?php
namespace Townspot\SeriesEpisode;

class Entity extends \Townspot\Entity
{
	protected $_id;

	protected $_season;

	protected $_media;
	
	protected $_series;

	protected $_episode_number;

	public function __construct()
	{
	}

	public function setSeason(\Townspot\SeriesSeason\Entity $value)
	{
		$this->_season = $value;
		return $this;
	}

	public function setMedia(\Townspot\Media\Entity $value)
	{
		$this->_media = $value;
		return $this;
	}
	
	public function setSeries(\Townspot\Series\Entity $value)
	{
		$this->_series = $value;
		return $this;
	}

	public function setEpisodeNumber($value)
	{
		$this->_episode_number = $value;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function getSeason()
	{
		return $this->_season;
	}

	public function getMedia()
	{
		return $this->_media;
	}
	
	public function getSeries()
	{
		return $this->_series;
	}

	public function getEpisodeNumber()
	{
		return $this->_episode_number;
	}
}