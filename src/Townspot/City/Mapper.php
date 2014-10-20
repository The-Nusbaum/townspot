<?php
namespace Townspot\City;
use Townspot\Doctrine\Mapper\AbstractEntityMapper;

class Mapper extends AbstractEntityMapper
{
	protected $_repositoryName = "Townspot\City\Entity";
	
	public function getIndexerRows($dateTime = null) 
	{
		$results = array();
		$sql = "SELECT city.id,city.name,province.name as province_name,province.abbrev as province_abbrev from `city` JOIN province on province.id = city.province_id where city.id in (SELECT distinct city_id from media)";
		if ($dateTime) {
			$sql .= " AND city.updated >= '" . $dateTime->format('Y-m-d H:i:s') . "'";
		}
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}
}