<?php
namespace Townspot\Lucene;

use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Document\Field;

class VideoIndex extends AbstractIndex 
{  
	protected $_index = 'video';

	public function build() 
	{
		$errors = array();
		$this->clear(true);
		$index = $this->getIndex();
		\ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(new \ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive());
		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		$seriesEpisodeMapper = new \Townspot\SeriesEpisode\Mapper($this->getServiceLocator());
		$media = $mediaMapper->findAll();
		foreach ($media as $obj) {
			try {
				$series_id 			= 0;
				$episode_number 	= 0;
				$series_name 		= '';
				$series_description = '';
				$categories = array();
				$episode = $seriesEpisodeMapper->findByMedia($obj);
				if (count($episode)) {
					$episode = array_shift($episode);
					$series_id 			= $episode->getSeries()->getId();
					$episode_number 	= $episode->getEpisodeNumber();
					$series_name 		= htmlentities(strtolower(strip_tags($episode->getSeries()->getName())));
					$series_description = htmlentities(strtolower(strip_tags($episode->getSeries()->getDescription())));
				}
				$doc = new Document();	
				$doc->addField(Field::Text('objectid', $obj->getId()));		
				$doc->addField(Field::Text('title', htmlentities($obj->getTitle())));		
				$doc->addField(Field::Text('userid', htmlentities($obj->getUser()->getId())));		
				$doc->addField(Field::Text('user', htmlentities($obj->getUser()->getUsername())));		
				$doc->addField(Field::Text('city', htmlentities($obj->getCity()->getName())));		
				$doc->addField(Field::Text('province', htmlentities($obj->getProvince()->getName())));		
				$doc->addField(Field::Text('user_city', htmlentities($obj->getUser()->getCity()->getName())));		
				$doc->addField(Field::Text('user_province', htmlentities($obj->getUser()->getProvince()->getName())));		
				$doc->addField(Field::Text('description', htmlentities(strip_tags($obj->getDescription()))));	
				$doc->addField(Field::Text('series_id', $series_id));
				$doc->addField(Field::Text('episode_number', $episode_number));
				$doc->addField(Field::Text('series_name', $series_name));
				$doc->addField(Field::Text('series_description', $series_description));
				foreach ($obj->getCategories() as $category) {
					$categories[] = htmlentities($category->getId());
				}
				$doc->addField(Field::Text('categories', implode('::',$categories)));		
				$index->addDocument($doc);
			} catch (\Doctrine\ORM\EntityNotFoundException $e) {
			} catch (\ZendGData\App\HttpException $e) {
			}
		}
	}
	
	public function add($object)
	{
		$index = $this->getIndex();
		$seriesEpisodeMapper = new \Townspot\SeriesEpisode\Mapper($this->getServiceLocator());
		\ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(new \ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive());
		try {
			$doc = new Document();	
			$doc->addField(Field::Text('objectid', $object->getId()));		
			$doc->addField(Field::Text('title', htmlentities($object->getTitle())));		
			$doc->addField(Field::Text('userid', htmlentities($object->getUser()->getId())));		
			$doc->addField(Field::Text('user', htmlentities($object->getUser()->getUsername())));		
			$doc->addField(Field::Text('city', htmlentities($object->getCity()->getName())));		
			$doc->addField(Field::Text('province', htmlentities($object->getProvince()->getName())));		
			$doc->addField(Field::Text('user_city', htmlentities($object->getUser()->getCity()->getName())));		
			$doc->addField(Field::Text('user_province', htmlentities($object->getUser()->getProvince()->getName())));		
			$doc->addField(Field::Text('description', htmlentities(strip_tags($object->getDescription()))));	
			if ($episode = $seriesEpisodeMapper->findOneByMedia($media)) {
				$doc->addField(Field::Text('series_id', htmlentities(strip_tags($episode->getSeries()->getId()))));	
				$doc->addField(Field::Text('series_name', htmlentities(strip_tags($episode->getSeries()->getName()))));	
				$doc->addField(Field::Text('series_description', htmlentities(strip_tags($episode->getSeries()->getDescription()))));	
				$doc->addField(Field::Text('episode_number', htmlentities($episode->getEpisodeNumber())));		
			} else {
				$doc->addField(Field::Text('series_id', null));	
				$doc->addField(Field::Text('series_name', null));	
				$doc->addField(Field::Text('series_description', null));	
				$doc->addField(Field::Text('episode_number', null));		
			}
			$categories = array();
			foreach ($object->getCategories() as $category) {
				$categories[] = $category->getId();
			}
			$doc->addField(Field::Text('categories', implode('::',$categories)));		
			$index->addDocument($doc);
		} catch (\Doctrine\ORM\EntityNotFoundException $e) {
		} catch (\ZendGData\App\HttpException $e) {
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
		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		$results = array();
		$matches = $this->getIndex()->find($query);
		foreach ($matches as $hit) {	
			$results[] = $mediaMapper->find($hit->objectid);
		}
		return $results;
	}
}