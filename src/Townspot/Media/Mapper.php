<?php
namespace Townspot\Media;
use Townspot\Doctrine\Mapper\AbstractEntityMapper;
use \Doctrine\ORM\Query\ResultSetMapping;

class Mapper extends AbstractEntityMapper
{
	protected $_repositoryName = "Townspot\Media\Entity";
	
	public function getClosest($lat,$long) 
	{
		$rsm = new ResultSetMapping();

		$sql = "SELECT id, ( 3959 * acos( cos( radians(%s) ) * cos( radians( latitude ) ) ";
		$sql .= "* cos( radians( longitude ) - radians(%s) ) + ";
		$sql .= "sin( radians(%s) ) * sin(radians(latitude)) ) ) AS distance ";
		$sql .= "from media HAVING distance < 10000 ";
		$sql .= "ORDER BY distance ";
		$sql .= "LIMIT 1;";
		$sql = sprintf($sql,$lat,$long,$lat);
		
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		if ($results = $stmt->fetchAll()) {
			$results = array_shift($results);
			if ($media = $this->find($results['id'])) {
				return $media;
			}
		}
		return null;
	}
	
	public function getMediaLike($object,$limit=3) 
	{
		$query = new \ZendSearch\Lucene\Search\Query\MultiTerm();
		$videoIndex = new \Townspot\Lucene\VideoIndex($this->getServiceLocator());
		$seriesEpisodeMapper = new \Townspot\SeriesEpisode\Mapper($this->getServiceLocator());
		$query->addTerm(new \ZendSearch\Lucene\Index\Term($object->getId(), 'objectid'), false);
		// Match Series
		$episode = $seriesEpisodeMapper->findByMedia($object);
		if (count($episode)) {
			$episode = array_shift($episode);
			$query->addTerm(new \ZendSearch\Lucene\Index\Term($episode->getSeries()->getId(), 'series_id'), null);
		}
		// Match User
		$query->addTerm(new \ZendSearch\Lucene\Index\Term($object->getUser()->getId(), 'userid'), null);
		// Match Categories
		foreach ($object->getCategories() as $category) {
			$query->addTerm(new \ZendSearch\Lucene\Index\Term($category->getId(), 'categories'), null);
		}
		$results = $videoIndex->find($query);
		$results = array_slice($results, 0, $limit);
		return $results;
	}
}
