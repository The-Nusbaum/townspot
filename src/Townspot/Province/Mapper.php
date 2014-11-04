<?php
namespace Townspot\Province;
use Townspot\Doctrine\Mapper\AbstractEntityMapper;

class Mapper extends AbstractEntityMapper
{
	protected $_repositoryName = "Townspot\Province\Entity";
	
	public function getProvincesHavingMedia($countryName = 'United States') 
	{
		$results = array();
		$countryMapper  = new \Townspot\Country\Mapper($this->getServiceLocator());
		$country  	= $countryMapper->findOneByName('United States');
		$sql = "SELECT DISTINCT media.province_id, province.name, count(media.id) as media_count FROM media ";
		$sql .= "JOIN province on media.province_id = province.id ";
		$sql .= "WHERE media.country_id = " . $country->getId();
		$sql .= " GROUP BY media.province_id ORDER BY province.name";
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		if ($_results = $stmt->fetchAll()) {
			foreach ($_results as $result) {
				$results[] = array(
					'id'			=> $result['province_id'],
					'name'			=> $result['name'],
					'media_count'	=> $result['media_count']
				);
			}
		}
		return $results;
	}
	
}
