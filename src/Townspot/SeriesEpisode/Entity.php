<?php
namespace Townspot\SeriesEpisode;

class Entity extends \Townspot\Entity
{
	protected $_id;

	protected $_name;

	protected $_description;

	protected $_season;

	protected $_media;

	protected $_episode_number;

	public function __construct()
	{
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

	public function setEpisodeNumber($value)
	{
		$this->_episode_number = $value;
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

	public function getSeason()
	{
		return $this->_season;
	}

	public function getMedia()
	{
		return $this->_media;
	}

	public function getEpisodeNumber()
	{
		return $this->_episode_number;
	}
}