<?php
namespace Townspot\Lucene;

use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Document\Field;

class SeriesIndex extends AbstractIndex 
{  
	protected $_index = 'series';

	public function build() 
	{
		$errors = array();
		$this->clear(true);
		$index = $this->getIndex();
		\ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(new \ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive());
		$seriesMapper = new \Townspot\Series\Mapper($this->getServiceLocator());
		$series = $seriesMapper->findAll();
		foreach ($series as $obj) {
			if (count($obj->getEpisodes())) {
				foreach ($obj->getEpisodes() as $episode) {
					try {
						$doc = new Document();	
						$doc->addField(Field::Text('objectid', $obj->getId()));		
						$doc->addField(Field::unStored('title', htmlentities($obj->getName())));		
						$doc->addField(Field::unStored('user', htmlentities($obj->getUser()->getUsername())));		
						$doc->addField(Field::unStored('description', htmlentities(strtolower(strip_tags($obj->getDescription())))));	
						$doc->addField(Field::unStored('episode_number', htmlentities($episode->getEpisodeNumber())));		
						$doc->addField(Field::unStored('episode_id', htmlentities($episode->getMedia()->getId())));		
						$doc->addField(Field::unStored('episode_title', htmlentities($episode->getMedia()->getTitle())));		
						$index->addDocument($doc);
					} catch (\Doctrine\ORM\EntityNotFoundException $e) {
					}
				}
			}
		}
	}
	
	public function add($object)
	{
		$index = $this->getIndex();
		\ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(new \ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive());
		if (count($object->getEpisodes())) {
			foreach ($object->getEpisodes() as $episode) {
				try {
					$doc = new Document();	
					$doc->addField(Field::Text('objectid', $object->getId()));		
					$doc->addField(Field::unStored('title', htmlentities($object->getName())));		
					$doc->addField(Field::unStored('user', htmlentities($object->getUser()->getUsername())));		
					$doc->addField(Field::unStored('description', htmlentities(strtolower(strip_tags($object->getDescription())))));	
					$doc->addField(Field::unStored('episode_id', htmlentities($object->getId())));		
					$doc->addField(Field::unStored('episode_number', htmlentities($episode->getEpisodeNumber())));		
					$doc->addField(Field::unStored('episode_mediaid', htmlentities($episode->getMedia()->getId())));		
					$doc->addField(Field::unStored('episode_title', htmlentities($episode->getMedia()->getTitle())));		
					$index->addDocument($doc);
				} catch (\Doctrine\ORM\EntityNotFoundException $e) {
				}
			}
		}
	}
	
	public function addEpisode($object)
	{
		$index = $this->getIndex();
		\ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(new \ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive());
		try {
			$doc = new Document();	
			$doc->addField(Field::Text('objectid', $object->getSeries()->getId()));		
			$doc->addField(Field::unStored('title', htmlentities($object->getSeries()->getTitle())));		
			$doc->addField(Field::unStored('user', htmlentities($object->getSeries()->getUser()->getUsername())));		
			$doc->addField(Field::unStored('city', htmlentities(strtolower($object->getSeries()->getCity()->getName()))));		
			$doc->addField(Field::unStored('province', htmlentities(strtolower($object->getSeries()->getProvince()->getName()))));		
			$doc->addField(Field::unStored('description', htmlentities(strtolower(strip_tags($object->getSeries()->getDescription())))));	
			$doc->addField(Field::unStored('episode_id', htmlentities($object->getId())));		
			$doc->addField(Field::unStored('episode_number', htmlentities($object->getEpisodeNumber())));		
			$doc->addField(Field::unStored('episode_mediaid', htmlentities($object->getMedia()->getId())));		
			$doc->addField(Field::unStored('episode_title', htmlentities($object->getMedia()->getTitle())));		
			$doc->addField(Field::unStored('episode_description', htmlentities($object->getMedia()->getDescription())));		
			$index->addDocument($doc);
		} catch (\Doctrine\ORM\EntityNotFoundException $e) {
		}
	}

	public function update($object)
	{
		$this->removeEpisode($object);
		$this->addEpisode($object);
	}

	public function updateEpisode($object)
	{
		$this->remove($object);
		$this->add($object);
	}

	public function remove($object)
	{
		$index = $this->getIndex();
		$match = null;
		$matches = $this->getIndex()->find('objectid:' . $object->getId());
		if ($matches) {
			foreach ($matches as $match) {
				$this->getIndex()->delete($match->id);
			}
		}
	}
	
	public function removeEpisode($object)
	{
		$index = $this->getIndex();
		$match = null;
		$matches = $this->getIndex()->find('episode_mediaid:' . $object->getId());
		if ($matches) {
			$match = array_shift($match);
			$this->getIndex()->delete($match->id);
		}
	}

	public function find($query)
	{
		$results = array();
		if (is_array($query)) {
			foreach ($query as $q) {
				$results = array_merge($results,$this->find($q));
			}
		} else {
			$matches = $this->getIndex()->find($query);
			foreach ($matches as $hit) {	
				$results[] = array(
					'id'			=> $hit->objectid,
					'username'		=> $hit->username,
					'city'			=> $hit->city,
					'province'		=> $hit->province,
					'_score'		=> $hit->score
				);
			}
		}
		return array_unique($results);
	}
}