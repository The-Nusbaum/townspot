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
				$indexcount++;
			}
			$this->optimize();
		}
	}

	public function add($row)
	{
		if ($row instanceof \Townspot\User\Entity) {
			$row = $this->_getArrayFromObject($row);
		}
		$index = $this->getIndex();
		\ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(new \ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive());
		try {
			$doc = new Document();	
			$doc->addField(Field::Text('objectid', $row['user_id']));	
			$doc->addField(Field::Text('username', htmlentities($row['username'])));	
			$doc->addField(Field::Text('artist_name', htmlentities($row['artist_name'])));	
			$doc->addField(Field::Text('email', htmlentities($row['email'])));	
			$doc->addField(Field::Text('created', strtotime($row['created'])));	
			$doc->addField(Field::Text('province', $row['province']));	
			$doc->addField(Field::Text('city', $row['city']));	
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
		if ($row instanceof \Townspot\User\Entity) {
			$row = $this->_getArrayFromObject($row);
		}
		$index = $this->getIndex();
		$match = null;
		$matches = $this->getIndex()->find('objectid:' . $row['user_id']);
		if ($matches) {
			$match = array_shift($matches);
			$this->getIndex()->delete($match->id);
		}
	}
	
	protected function _getArrayFromObject($obj)
	{
		return array(
			'user_id'			=> $obj->getId(),
			'username'			=> $obj->getUsername(),
			'artist_name'		=> $obj->getArtistName(),
			'email'				=> $obj->getEmail(),
			'city'				=> $obj->getCity()->getName(),
			'province'			=> $obj->getProvince()->getName(),
			'created'			=> $obj->getCreated()->format('U')
		);
	}
}