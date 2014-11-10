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
}
