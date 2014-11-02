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
				$indexcount++;
			}
			$this->optimize();
		}
	}

	public function add($row)
	{
		if ($row instanceof \Townspot\Media\Entity) {
			$row = $this->_getArrayFromObject($row);
		}
		$index = $this->getIndex();
		\ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(new \ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive());
		$row['series'] = $row['series'] ?: '';
		$row['episode_number'] = $row['episode_number'] ?: 0;
		try {
			$doc = new Document();	
			$doc->addField(Field::Text('objectid', $row['id']));	
			$doc->addField(Field::Text('title', $row['title']));	
			$doc->addField(Field::Text('views', $row['views']));	
			$doc->addField(Field::Text('created', strtotime($row['created'])));	
			$doc->addField(Field::Text('categories', $row['categories']));	
			$doc->addField(Field::Text('logline', htmlentities(strip_tags($row['logline']))));	
			$doc->addField(Field::Text('description', htmlentities(strip_tags($row['description']))));	
			$doc->addField(Field::Text('user', $row['user']));	
			$doc->addField(Field::Text('city', $row['city']));	
			$doc->addField(Field::Text('province', $row['province']));	
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
		if ($row instanceof \Townspot\Media\Entity) {
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
		$row = array(
			'id'				=> $obj->getId(),
			'title'				=> $obj->getTitle(),
			'logline'			=> $obj->getLogline(),
			'description'		=> $obj->getDescription(),
			'views'				=> $obj->getViews(false),
			'created'			=> $obj->getCreated()->format('Y-m-d H:i:s'),
			'user'				=> $obj->getUser()->getUsername(),
			'city'				=> $obj->getCity()->getName(),
			'province'			=> $obj->getProvince()->getName(),
		);
		$categories = array();
		foreach ($obj->getCategories() as $category) {
			$categories[] = $category->getName();
		}
		$row['categories'] = implode('::',$categories);
		return $row;
	}
}