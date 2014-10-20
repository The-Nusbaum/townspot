<?php
namespace Townspot\City;
use Townspot\Doctrine\Mapper\AbstractEntityMapper;

class Mapper extends AbstractEntityMapper
{
	protected $_repositoryName = "Townspot\City\Entity";
	
	public function getIndexerRows($dateTime = null) 
	{
		$results = array();
		$sql = "SELECT city.id,city.name,province.name as province_name,province.abbrev as province_abbrev,count(media.id) as media_count FROM `city` JOIN province on province.id = city.province_id JOIN media on media.city_id = city.id"
		if ($dateTime) {
			$sql .= " WHERE city.updated >= '" . $dateTime->format('Y-m-d H:i:s') . "'";
		}
		$sql .= " GROUP BY city.id";
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}
}