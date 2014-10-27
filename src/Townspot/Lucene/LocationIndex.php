<?php
namespace Townspot\Lucene;

use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Document\Field;

class LocationIndex extends AbstractIndex 
{  
	protected $_index = 'location';

	public function build() 
	{
		$this->clear(true);
		$index = $this->getIndex();
		\ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(new \ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive());
		$cityMapper = new \Townspot\City\Mapper($this->getServiceLocator());
		if ($rows = $cityMapper->getIndexerRows()) {
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
		$cityMapper = new \Townspot\City\Mapper($this->getServiceLocator());
		if ($rows = $cityMapper->getIndexerRows($datetime)) {
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
//		if ($row instanceof \Townspot\City\Entity) {
//			$row = $this->_getArrayFromObject($row);
//		}
		$index = $this->getIndex();
		\ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(new \ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive());
		try {
			$doc = new Document();	
			$doc->addField(Field::Text('objectid', $row['id']));	
			$doc->addField(Field::Text('city_name', htmlentities($row['name'])));	
			$doc->addField(Field::Text('province_name', htmlentities($row['province_name'])));	
			$doc->addField(Field::Text('province_abbrev', htmlentities($row['province_abbrev'])));	
			$index->addDocument($doc);
		} catch (\Doctrine\ORM\EntityNotFoundException $e) {
		} catch (\ZendSearch\Lucene\Exception\RuntimeException $e) {
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
//		if ($row instanceof \Townspot\City\Entity) {
//			$row = $this->_getArrayFromObject($row);
//		}
		$index = $this->getIndex();
		$match = null;
		$matches = $this->getIndex()->find('objectid:' . $row['id']);
		if ($matches) {
			$match = array_shift($matches);
			$this->getIndex()->delete($match->id);
		}
	}
	
	public function find($query,$sortField = null,$sortType = null,$sortOrder = null)
	{
		$cityMapper = new \Townspot\City\Mapper($this->getServiceLocator());
		$results = array();
		if ($sortField) {
			$matches = $this->getIndex()->find($query,$sortField);
		} elseif (($sortField)&&($sortType)) {
			$matches = $this->getIndex()->find($query,$sortField,$sortType);
		} elseif (($sortField)&&($sortType)&&($sortOrder)) {
			$matches = $this->getIndex()->find($query,$sortField,$sortType,$sortOrder);
		} else {
			$matches = $this->getIndex()->find($query);
		}
		foreach ($matches as $hit) {	
			$results[] = $cityMapper->find($hit->objectid);
		}
		return $results;
	}

	protected function _getArrayFromObject($obj)
	{
//		return array(
//			'id'				=> $obj->getId(),
//			'name'				=> $obj->getName(),
//			'province_name'		=> $obj->getProvince()->getName(),
//			'province_abbrev'	=> $obj->getProvince()->getAbbrev()
//		);
	}
}