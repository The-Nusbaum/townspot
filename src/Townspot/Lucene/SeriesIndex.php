<?php
namespace Townspot\Lucene;

use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Document\Field;

class SeriesIndex extends AbstractIndex 
{  
	protected $_index = 'series';

	public function build() 
	{
		$this->clear(true);
		$index = $this->getIndex();
		\ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(new \ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive());
		$seriesMapper = new \Townspot\Series\Mapper($this->getServiceLocator());
		if ($rows = $seriesMapper->getIndexerRows()) {
			$totalcount = count($rows);
			$indexcount = 1;
			foreach ($rows as $row) {
				print $indexcount . "/" . $totalcount . "\n";
				$this->add($row);
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
		$seriesMapper = new \Townspot\Series\Mapper($this->getServiceLocator());
		if ($rows = $seriesMapper->getIndexerRows($datetime)) {
			$totalcount = count($rows);
			$indexcount = 1;
			foreach ($rows as $row) {
				print $indexcount . "/" . $totalcount . "\n";
				$this->update($row);
				$indexcount++;
			}
			$this->optimize();
		}
	}

	public function add($row)
	{
		if ($row instanceof \Townspot\Series\Entity) {
			$row = $this->_getArrayFromObject($row);
		}
		$index = $this->getIndex();
		\ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(new \ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive());
		try {
			$doc = new Document();	
			$doc->addField(Field::Text('objectid', $row['id']));	
			$doc->addField(Field::Text('name', htmlentities($row['name'])));	
			$doc->addField(Field::Text('description', htmlentities(strip_tags($row['description']))));	
			$doc->addField(Field::Text('media_descriptions', htmlentities(strip_tags($row['media_descriptions']))));	
			$doc->addField(Field::Text('media_titles', htmlentities(strip_tags($row['media_titles']))));	
			$doc->addField(Field::Text('media_loglines', htmlentities(strip_tags($row['media_loglines']))));	
			$doc->addField(Field::Text('created', strtotime($row['created'])));	
			$index->addDocument($doc);
		} catch (\Doctrine\ORM\EntityNotFoundException $e) {
		} catch (\ZendSearch\Lucene\Exception\RuntimeException $e) {
		} catch (\ZendGData\App\HttpException $e) {
		}
		sleep(1);
	}
	
	public function update($row)
	{
		$this->remove($row);
		$this->add($row);
	}

	public function remove($row)
	{
		if ($row instanceof \Townspot\Series\Entity) {
			$row = $this->_getArrayFromObject($row);
		}
		$index = $this->getIndex();
		$match = null;
		$matches = $this->getIndex()->find('objectid:' . $row['id']);
		if ($matches) {
			$match = array_shift($matches);
			$this->getIndex()->delete($match->id);
		}
	}
	
	protected function _getArrayFromObject($obj)
	{
		return array(
			'id'			=> $obj->getId(),
			'name'			=> $obj->getName(),
			'description'	=> $obj->getDescription(),
			'created'			=> $obj->getCreated()->format('U')
		);
	}
}