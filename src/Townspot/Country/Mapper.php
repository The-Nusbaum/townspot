<?php
namespace Townspot\Country;
use Townspot\Doctrine\Mapper\AbstractEntityMapper;

class Mapper extends AbstractEntityMapper
{
	protected $_repositoryName = "Townspot\Country\Entity";

	public function getCountriesHavingMedia() 
	{
		$results = array();
		$sql = "SELECT DISTINCT media.country_id, country.name, count(media.id) as media_count FROM media ";
		$sql .= "JOIN country on media.country_id = country.id ";
		$sql .= "WHERE media.approved = 1";
		$sql .= " GROUP BY media.country_id ORDER BY country.name";
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		if ($_results = $stmt->fetchAll()) {
			foreach ($_results as $result) {
				$results[] = array(
					'id'			=> $result['country_id'],
					'name'			=> $result['name'],
					'media_count'	=> $result['media_count']
				);
			}
		}
		return $results;
	}
}
