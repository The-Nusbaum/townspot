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
		$sql .= " media.id, ";
		$sql .= " media.title, ";
		$sql .= " media.logline, ";
		$sql .= " media.description, ";
		$sql .= " media.user_id, ";
		$sql .= " media.city_id, ";
		$sql .= " media.province_id, ";
		$sql .= " media.views, ";
		$sql .= " media.created, ";
		$sql .= " GROUP_CONCAT(category.name SEPARATOR '::') as categories ";
		$sql .= "FROM  ";
		$sql .= " media ";
		$sql .= "JOIN media_category_linker on media_category_linker.media_id = media.id ";
		$sql .= "JOIN category on media_category_linker.category_id = category.id ";
		$sql .= "LEFT JOIN series_episodes on series_episodes.media_id = media.id ";
		$sql .= "WHERE media.approved = 1 ";
		$sql .= "AND (series_episodes.series_id IS NULL) ";
		if ($dateTime) {
			$sql .= " AND media.updated >= '" . $dateTime->format('Y-m-d H:i:s') . "'";
		}
		$sql .= " GROUP BY id";
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function getMediaLike($object,$limit=3) 
	{
		$results = array();
		$seriesId = 0;
		$seriesEpisodeMapper = new \Townspot\SeriesEpisode\Mapper($this->getServiceLocator());
		// Match Series
		$episode = $seriesEpisodeMapper->findByMedia($object);
		if (count($episode)) {
			$episode = array_shift($episode);
			$seriesId = $episode->getSeries()->getId();
			$episodes = $seriesEpisodeMapper->findBySeries($episode->getSeries());
			foreach ($episodes as $_episode) {
				if ($_episode->getEpisodeNumber() > $episode->getEpisodeNumber()) {
					if ($_episode->getMedia()->getApproved()) {
						$results[] = $_episode->getMedia()->getId();
					}
				}
			}
		}
		//Match User Series
		if (count($results) < $limit) {
			$_randResults = array();
			$sql  = "SELECT ";
			$sql .= " series_episodes.media_id ";
			$sql .= "FROM  ";
			$sql .= " series ";
			$sql .= " JOIN series_episodes on series_episodes.series_id = series.id ";
			$sql .= " JOIN media on series_episodes.media_id = media.id ";
			$sql .= "WHERE series.user_id = " . $object->getUser()->getId();
			$sql .= " AND series.id != " . $seriesId;
			$sql .= " AND series_episodes.episode_number = 1";
			$sql .= " AND media.approved = 1";
			$sql .= " ORDER BY RAND()";
			$sql .= " LIMIT " . $limit;
			$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
			$stmt->execute();
			if ($result = $stmt->fetchAll()) {
				foreach ($result as $_result) {
					$results[] = $_result['media_id'];
				}
			}
		}
		if (count($results) < $limit) {
			$sql  = "SELECT ";
			$sql .= " media.id ";
			$sql .= "FROM  ";
			$sql .= " media ";
			$sql .= "WHERE media.user_id = " . $object->getUser()->getId();
			$sql .= " AND media.id != " . $object->getId();
			$sql .= " AND media.approved = 1";
			$sql .= " ORDER BY RAND()";
			$sql .= " LIMIT " . $limit;
			$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
			$stmt->execute();
			if ($result = $stmt->fetchAll()) {
				foreach ($result as $_result) {
					$results[] = $_result['id'];
				}
			}
		}
		if (count($results) < $limit) {
			//Get Categories
			$sql  = "SELECT category_id from media_category_linker where media_id=" . $object->getId();
			$categories = array();
			$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
			$stmt->execute();
			if ($result = $stmt->fetchAll()) {
				foreach ($result as $_result) {
					$categories[] = $_result['category_id'];
				}
			}
			$sql  = "SELECT DISTINCT media_id from media_category_linker";
			$sql .= " JOIN media on media_category_linker.media_id = media.id ";
			$sql .= " WHERE media_category_linker.category_id IN (" . implode(',',$categories) . ")";
			$sql .= " AND media.id != " . $object->getId();
			$sql .= " AND media.approved = 1";
			$sql .= " ORDER BY RAND()";
			$sql .= " LIMIT " . $limit;
			$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
			$stmt->execute();
			if ($result = $stmt->fetchAll()) {
				foreach ($result as $_result) {
					$results[] = $_result['media_id'];
				}
			}
		}
		$results = array_slice($results,0,$limit);
		foreach ($results as $index => $media_id) {
			$results[$index] = $this->find($media_id);
		}
		return $results;
	}
	
	public function getDiscoverMedia($province_id = null,$city_id=null,$category_id = null,$sort = 'created:desc') 
	{
		$results = array();
		$sql  = "SELECT ";
		$sql .= " DISTINCT media.id ";
		$sql .= "FROM  ";
		$sql .= " media ";
		$sql .= "JOIN media_category_linker on media_category_linker.media_id = media.id ";
		$where = array('media.approved = 1');
		if ($province_id) {
			$where[] = 'media.province_id=' . $province_id;
		}
		if ($city_id) {
			$where[] = 'media.city_id=' . $city_id;
		}
		if ($category_id) {
			$where[] = 'media_category_linker.category_id=' . $category_id;
		}
		if ($where) {
			$sql .= " WHERE " . implode(' AND ',$where);
		}
        list($sortField,$sortOrder) = explode(':',$sort);
        if ($sortField == 'title') {
			$sql .= " ORDER BY media.title";
		} elseif ($sortField == 'views') {
			$sql .= " ORDER BY media.views";
        } else {
			$sql .= " ORDER BY media.created";
        }
		$sql .= ($sortOrder == 'asc') ? ' ASC' : ' DESC';
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}
}
