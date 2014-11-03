<?php
namespace Townspot\City;
use Townspot\Doctrine\Mapper\AbstractEntityMapper;

class Mapper extends AbstractEntityMapper
{
	protected $_repositoryName = "Townspot\City\Entity";
	
	public function getCitiesHavingMedia($province_id) 
	{
		$results = array();
		$sql = "SELECT DISTINCT media.city_id FROM media ";
		$sql .= "WHERE media.province_id=" . $province_id;
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		if ($_results = $stmt->fetchAll()) {
			foreach ($_results as $result) {
				$results[] = $this->find($result['city_id']);
			}
		}
		return $results;
	}

	public function getIndexerRows($dateTime = null) 
	{
		$results = array();
		$sql = "SELECT city.id,
					   city.name as city,
					   province.name as province,
					   province.abbrev as province_abbrev,
					   count(media.id) as media_count 
					   FROM `city` JOIN province on province.id = city.province_id 
					   JOIN media on media.city_id = city.id";
		if ($dateTime) {
			$sql .= " WHERE city.updated >= '" . $dateTime->format('Y-m-d H:i:s') . "'";
		}
		$sql .= " GROUP BY city.id";
		
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}
}