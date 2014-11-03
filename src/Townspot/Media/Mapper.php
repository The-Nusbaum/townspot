<?php
namespace Townspot\Media;
use TownspotBase\Doctrine\Mapper\AbstractEntityMapper;
use \Doctrine\ORM\Query\ResultSetMapping;

class Mapper extends AbstractEntityMapper
{
	protected $_repositoryName = "Townspot\Media\Entity";
	
	public function getClosest($lat,$long) 
	{
		$rsm = new ResultSetMapping();

		$sql = "SELECT id, ( 3959 * acos( cos( radians(%s) ) * cos( radians( latitude ) ) ";
		$sql .= "* cos( radians( longitude ) - radians(%s) ) + ";
		$sql .= "sin( radians(%s) ) * sin(radians(latitude)) ) ) AS distance ";
		$sql .= "from media HAVING distance < 10000 ";
		$sql .= "ORDER BY distance ";
		$sql .= "LIMIT 1;";
		$sql = sprintf($sql,$lat,$long,$lat);
		
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		if ($results = $stmt->fetchAll()) {
			$results = array_shift($results);
			if ($media = $this->find($results['id'])) {
				return $media;
			}
		}
		return null;
	}
}
