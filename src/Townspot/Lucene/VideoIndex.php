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
		$this->clear(true);
		$index = $this->getIndex();
		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		$media = $mediaMapper->findAll();
		foreach ($media as $obj) {
			$doc = new Document();	
			$doc->addField(Field::Text('mediaid', $obj->getId()));		
			$doc->addField(Field::unStored('title', htmlentities($obj->getTitle())));		
			$doc->addField(Field::unStored('user', htmlentities($obj->getUser()->getUsername())));		
			$doc->addField(Field::unStored('duration', htmlentities($obj->getDuration())));		
			$doc->addField(Field::unStored('city', htmlentities(strtolower($obj->getCity()->getName()))));		
			$doc->addField(Field::unStored('province', htmlentities(strtolower($obj->getProvince()->getName()))));		
			$doc->addField(Field::unStored('description', htmlentities(strtolower(strip_tags($obj->getDescription())))));		
			$categories = array();
			foreach ($obj->getCategories() as $category) {
				$categories[] = htmlentities($category->getName());
			}
			$doc->addField(Field::unStored('categories', implode('::',$categories)));		
			$index->addDocument($doc);
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
}