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
        $sql .= " AND media.approved = 1";
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

    public function getList($country_id = 99) {
        $sql = 'select id,name from province ';
        $sql .= "where country_id = $country_id";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
	
}
