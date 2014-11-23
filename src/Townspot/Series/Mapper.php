<?php
namespace Townspot\Series;
use Townspot\Doctrine\Mapper\AbstractEntityMapper;

class Mapper extends AbstractEntityMapper
{
	protected $_repositoryName = "Townspot\Series\Entity";
	
	public function getIndexerRows($dateTime = null) 
	{
		$results = array();
		$sql  = "SELECT series.id,
						series.name,
						series.description,
						series.created,
						media.city_id, 
						media.province_id, 
						GROUP_CONCAT(media.description SEPARATOR ' ') as media_descriptions,
						GROUP_CONCAT(media.title SEPARATOR ' ') as media_titles,
						GROUP_CONCAT(media.logline SEPARATOR ' ') as media_loglines
				 FROM series
				 JOIN series_episodes on series_episodes.series_id = series.id
				 JOIN media on series_episodes.media_id = media.id
			     WHERE media.approved = 1 ";
		if ($dateTime) {
			$sql .= " AND (media.updated >= '" . $dateTime->format('Y-m-d H:i:s') . "'";
			$sql .= " OR series.updated >= '" . $dateTime->format('Y-m-d H:i:s') . "')";
		}
		$sql .= " GROUP BY series.id";
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}
	
	public function getAdminList($options = array()) 
	{
		$where = array();
		$sort_field = 'user_id';
		$sort_order = 'ASC';
		$sql  = "SELECT series.id as id,
						series.name as title,
						user.username as username,
						count(series_episodes.id) as `episodecount`
						from series 
						JOIN user on user.user_id = series.user_id
						LEFT JOIN series_episodes on series_episodes.series_id = series.id";
		foreach ($options as $key => $value) {
			if ($value) {
				switch ($key) {
					case 'title':
						$where[] = "series.name LIKE '" . $value . "%'";
						break;
					case 'username':
						$where[] = "user.username LIKE '" . $value . "%'";
						break;
					case 'sort_field':
						$sort_field = $value;
						break;
					case 'sort_order':
						$sort_order = $value;
						break;
				}
			}
		}
		if ($where) {
			$sql .= " WHERE " . implode(' AND ',$where);
		} 
		$sql .= " GROUP BY series.id";
		$sql .= " ORDER BY " . $sort_field . " " . $sort_order;
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}
	
	public function delete() 
	{
        $entity = $this->getEntity();
		$sql  = "DELETE from series_episodes where series_episodes.series_id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$sql  = "DELETE from series_season where series_season.series_id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$sql  = "DELETE from series where id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
	}
}
