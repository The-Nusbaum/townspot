<?php
namespace Townspot\Province;
use Townspot\Doctrine\Mapper\AbstractEntityMapper;

class Mapper extends AbstractEntityMapper
{
	protected $_repositoryName = "Townspot\Province\Entity";
	
	public function getCitiesHavingMedia($countryName = 'United States') 
	{
		$results = array();
		$country  	= $countryMapper->findOneByName('United States');
		$sql = "SELECT DISTINCT media.province_id FROM media ";
		$sql .= "WHERE media.country_id = " . $country->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		if ($_results = $stmt->fetchAll()) {
			foreach ($_results as $result) {
				$results[] = $this->find($result['province_id']);
			}
		}
		return $results;
	}
	
}
