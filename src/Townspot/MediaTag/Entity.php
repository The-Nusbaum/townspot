<?php
namespace Townspot\MediaTag;

class Entity extends \Townspot\Entity
{
	protected $_id;

	protected $_media;

	protected $_tag;

	public function __construct()
	{
	}

	public function setMedia(\Townspot\Media\Entity $value)
	{
		$this->_media = $value;
		return $this;
	}

	public function setTag($value)
	{
		$this->_tag = $value;
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

	public function getTag()
	{
		return $this->_tag;
	}
}