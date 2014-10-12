<?php
namespace Townspot\Lucene;

use TownspotBase\Lucene\Index\SearchIndex;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Document\Field;

class VideoIndex extends SearchIndex 
{  
	protected $_index = 'video';

	public function build() 
	{
		$errors = array();
		$this->clear(true);
		$index = $this->getIndex();
		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		$media = $mediaMapper->findAll();
		foreach ($media as $obj) {
			try {
				$doc = new Document();	
				$doc->addField(Field::Text('mediaid', $obj->getId()));		
				$doc->addField(Field::unStored('title', htmlentities($obj->getTitle())));		
				$doc->addField(Field::unStored('user', htmlentities($obj->getUser()->getUsername())));		
//				$doc->addField(Field::unStored('duration', htmlentities($obj->getDuration())));		
				$doc->addField(Field::unStored('city', htmlentities(strtolower($obj->getCity()->getName()))));		
				$doc->addField(Field::unStored('province', htmlentities(strtolower($obj->getProvince()->getName()))));		
				$doc->addField(Field::unStored('description', htmlentities(strtolower(strip_tags($obj->getDescription())))));		
				$categories = array();
				foreach ($obj->getCategories() as $category) {
					$categories[] = htmlentities($category->getName());
				}
				$doc->addField(Field::unStored('categories', implode('::',$categories)));		
			} catch (\Doctrine\ORM\EntityNotFoundException $e) {
				$doc->addField(Field::unStored('user', ''));
				$errors[] = $obj->getId();
			} catch (\ZendGData\App\HttpException $e) {
				// I don't care about this error
			}
			$index->addDocument($doc);
				
		}
		print "Detected Errors - missing User Id\n";
		print "=================================\n";
		foreach ($errors as $error) {
			print $error . "\n";
		}

	}
	
	public function add($object)
	{

	}
	
	public function update($object)
	{

	}

	public function remove($object)
	{
	
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
				$results[] = $hit->mediaid; 
			}
		}
		return array_unique($results);
	}
}