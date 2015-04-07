<?php
namespace Townspot\City;
use Townspot\Doctrine\Mapper\AbstractEntityMapper;

class Mapper extends AbstractEntityMapper
{
	protected $_repositoryName = "Townspot\City\Entity";
	
	public function getCitiesHavingMedia($province_id) 
	{
		$results = array();
		$sql = "SELECT DISTINCT media.city_id, city.name, count(media.id) as media_count FROM media ";
		$sql .= "JOIN city on media.city_id = city.id ";
		$sql .= "WHERE media.province_id = " . $province_id;
		$sql .= " AND media.approved = 1";
		$sql .= " GROUP BY media.city_id ORDER BY city.name";
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		if ($_results = $stmt->fetchAll()) {
			foreach ($_results as $result) {
				$results[] = array(
					'id'			=> $result['city_id'],
					'name'			=> $result['name'],
					'media_count'	=> $result['media_count']
				);
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
					   JOIN media on media.city_id = city.id
					   WHERE media.approved = 1 ";
		if ($dateTime) {
			$sql .= " AND (media.updated >= '" . $dateTime->format('Y-m-d H:i:s') . "'";
			$sql .= " OR city.updated >= '" . $dateTime->format('Y-m-d H:i:s') . "')";
		}
		$sql .= " GROUP BY city.id";
		
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}
}