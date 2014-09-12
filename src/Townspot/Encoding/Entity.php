<?php
namespace Townspot\Encoding;

class Entity
{
	protected $_id;

	protected $_encoding_id;

	protected $_status;
	
	protected $_media;

	public function __construct()
	{
	}

	public function setEncodingId($value)
	{
		$this->_encoding_id = $value;
		return $this;
	}

	public function setStatus($value)
	{
		$this->_status = $value;
		return $this;
	}

	public function setMedia(\Townspot\Media\Entity $value)
	{
		$this->_media = $value;
		return $this;
	}
	
	public function getId()
	{
		return $this->_id;
	}

	public function getEncodingId()
	{
		return $this->_encoding_id;
	}

	public function getStatus()
	{
		return $this->_status;
	}

	public function getMedia()
	{
		return $this->_media;
	}
}