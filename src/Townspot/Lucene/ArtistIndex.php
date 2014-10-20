<?php
namespace Townspot\Lucene;

use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Document\Field;

class ArtistIndex extends AbstractIndex 
{  
	protected $_index = 'artist';

	public function build() 
	{
		$this->clear(true);
		$index = $this->getIndex();
		\ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(new \ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive());
		$userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
		if ($rows = $userMapper->getIndexerRows()) {
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
		$userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
		if ($rows = $userMapper->getIndexerRows($datetime)) {
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
		try {
			$doc = new Document();	
			$doc->addField(Field::Text('objectid', $row['user_id']));	
			$doc->addField(Field::Text('username', htmlentities($row['username'])));	
			$doc->addField(Field::Text('artist_name', htmlentities($row['artist_name'])));	
			$doc->addField(Field::Text('email', htmlentities($row['email'])));	
			$doc->addField(Field::UnStored('city_id', $row['city_id']));	
			$doc->addField(Field::UnStored('province_id', $row['province_id']));	
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
		$matches = $this->getIndex()->find('objectid:' . $row['user_id']);
		if ($matches) {
			$match = array_shift($matches);
			$this->getIndex()->delete($match->id);
		}
	}
	
	public function find($query,$sortField = null,$sortType = null,$sortOrder = null)
	{
		$userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
		$results = array();
		$matches = $this->getIndex()->find($query,$sortField,$sortType,$sortOrder);
		foreach ($matches as $hit) {	
			$results[] = $userMapper->find($hit->objectid);
		}
		return $results;
	}
}