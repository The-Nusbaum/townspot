<?php
namespace Townspot\Lucene;

use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Document\Field;

class VideoIndex extends AbstractIndex 
{  
	protected $_index = 'video';

	public function build() 
	{
		$this->clear(true);
		$index = $this->getIndex();
		\ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(new \ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive());
		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		if ($rows = $mediaMapper->getIndexerRows()) {
			$totalcount = count($rows);
			$indexcount = 1;
			foreach ($rows as $row) {
				print $indexcount . "/" . $totalcount . "\n";
				$this->add($row);
				if (($indexcount % 20) == 0) {
					$this->optimize();
					print "Optimizing\n";
				}
				$indexcount++;
			}
			$this->optimize();
		}
	}
	
	public function delta() 
	{
		$datetime = new \DateTime();
		$datetime->sub(new \DateInterval('PT15M'));
		$index = $this->getIndex();
		\ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(new \ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive());
		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		if ($rows = $mediaMapper->getIndexerRows($datetime)) {
			$totalcount = count($rows);
			$indexcount = 1;
			foreach ($rows as $row) {
				print $indexcount . "/" . $totalcount . "\n";
				$this->update($row);
				if (($indexcount % 20) == 0) {
					$this->optimize();
					print "Optimizing\n";
				}
				$indexcount++;
			}
			$this->optimize();
		}
	}

	public function add($row)
	{
		$index = $this->getIndex();
		\ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(new \ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive());
		$row['series_id'] = $row['series_id'] ?: 0;
		$row['episode_number'] = $row['episode_number'] ?: 0;
		try {
			$doc = new Document();	
			$doc->addField(Field::Text('objectid', $row['objectid']));	
			$doc->addField(Field::Text('title', $row['title']));	
			$doc->addField(Field::Text('views', $row['views']));	
			$doc->addField(Field::Text('created', $row['created']));	
			$doc->addField(Field::Text('categories', $row['categories']));	
			$doc->addField(Field::Text('logline', htmlentities(strip_tags($row['logline']))));	
			$doc->addField(Field::Text('description', htmlentities(strip_tags($row['description']))));	
			$doc->addField(Field::Text('user_id', $row['user_id']));	
			$doc->addField(Field::Text('city_id', $row['city_id']));	
			$doc->addField(Field::Text('province_id', $row['province_id']));	
			$doc->addField(Field::Text('series_id', $row['series_id']));	
			$doc->addField(Field::Text('episode_number', $row['episode_number']));	
			$index->addDocument($doc);
		} catch (\Doctrine\ORM\EntityNotFoundException $e) {
		} catch (\ZendGData\App\HttpException $e) {
		}
	}
	
	public function update($row)
	{
		$this->remove($row);
		$this->add($row);
	}

	public function remove($row)
	{
		$index = $this->getIndex();
		$match = null;
		$matches = $this->getIndex()->find('objectid:' . $row['objectid']);
		if ($matches) {
			$match = array_shift($matches);
			$this->getIndex()->delete($match->id);
		}
	}
	
	public function find($query,$sortField = null,$sortType = null,$sortOrder = null)
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