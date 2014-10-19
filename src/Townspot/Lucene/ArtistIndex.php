<?php
namespace Townspot\Lucene;

use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Document\Field;

class ArtistIndex extends AbstractIndex 
{  
	protected $_index = 'artist';

	public function build() 
	{
		$errors = array();
		$this->clear(true);
		$index = $this->getIndex();
		\ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(new \ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive());
		$userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
		$users = $userMapper->findAll();
		foreach ($users as $obj) {
			if (count($obj->getMedia())) {
				try {
					$doc = new Document();	
					$doc->addField(Field::Text('objectid', $obj->getId()));		
					$doc->addField(Field::unStored('username', htmlentities($obj->getUsername())));		
					$doc->addField(Field::unStored('city', htmlentities(strtolower($obj->getCity()->getName()))));		
					$doc->addField(Field::unStored('province', htmlentities(strtolower($obj->getProvince()->getName()))));		
					$index->addDocument($doc);
				} catch (\Doctrine\ORM\EntityNotFoundException $e) {
					// Ignore this error, field mau not be indexed
				}
			}
		}
	}
	
	public function add($object)
	{
		$index = $this->getIndex();
		\ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(new \ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive());
		try {
			$doc = new Document();	
			$doc->addField(Field::Text('objectid', $object->getId()));		
			$doc->addField(Field::unStored('username', htmlentities($object->getUsername())));		
			$doc->addField(Field::unStored('city', htmlentities(strtolower($object->getCity()->getName()))));		
			$doc->addField(Field::unStored('province', htmlentities(strtolower($object->getProvince()->getName()))));		
			$index->addDocument($doc);
		} catch (\Doctrine\ORM\EntityNotFoundException $e) {
		}
	}
	
	public function update($object)
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
		return $results;
	}
}