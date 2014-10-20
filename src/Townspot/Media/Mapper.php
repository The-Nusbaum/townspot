<?php
namespace Townspot\Media;
use Townspot\Doctrine\Mapper\AbstractEntityMapper;
use \Doctrine\ORM\Query\ResultSetMapping;

class Mapper extends AbstractEntityMapper
{
	protected $_repositoryName = "Townspot\Media\Entity";
	
	public function getClosest($lat,$long) 
	{
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
	
	public function getIndexerRows($dateTime = null) 
	{
		$results = array();
		$sql  = "SELECT ";
		$sql .= " media.id as objectid, ";
		$sql .= " media.title, ";
		$sql .= " media.logline, ";
		$sql .= " media.description, ";
		$sql .= " media.user_id, ";
		$sql .= " media.city_id, ";
		$sql .= " media.province_id, ";
		$sql .= " media.views, ";
		$sql .= " media.created, ";
		$sql .= " series_episodes.series_id, ";
		$sql .= " series_episodes.episode_number, ";
		$sql .= " GROUP_CONCAT(media_category_linker.category_id SEPARATOR '::') as categories ";
		$sql .= "FROM  ";
		$sql .= " media ";
		$sql .= "LEFT JOIN series_episodes on series_episodes.media_id = media.id ";
		$sql .= "LEFT JOIN media_category_linker on media_category_linker.media_id = media.id ";
		if ($dateTime) {
			$sql .= " WHERE media.updated >= '" . $dateTime->format('Y-m-d H:i:s') . "'";
		}
		$sql .= " GROUP BY objectid";
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function getMediaLike($object,$limit=3) 
	{
		$query = new \ZendSearch\Lucene\Search\Query\MultiTerm();
		$videoIndex = new \Townspot\Lucene\VideoIndex($this->getServiceLocator());
		$query->addTerm(new \ZendSearch\Lucene\Index\Term($object->getId(), 'objectid'), false);
		$seriesEpisodeMapper = new \Townspot\SeriesEpisode\Mapper($this->getServiceLocator());
		// Match Series
		$episode = $seriesEpisodeMapper->findByMedia($object);
		if (count($episode)) {
			$episode = array_shift($episode);
			$query->addTerm(new \ZendSearch\Lucene\Index\Term($episode->getSeries()->getId(), 'series_id'), null);
		}
		// Match User
		$query->addTerm(new \ZendSearch\Lucene\Index\Term($object->getUser()->getId(), 'user_id'), null);
		// Match Categories
		foreach ($object->getCategories() as $category) {
			$query->addTerm(new \ZendSearch\Lucene\Index\Term($category->getId(), 'categories'), null);
		}
		$results = $videoIndex->find($query);
		$results = array_slice($results, 0, $limit);
		return $results;
	}
}
