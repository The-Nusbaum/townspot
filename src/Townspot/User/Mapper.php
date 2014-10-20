<?php
namespace Townspot\User;
use Townspot\Doctrine\Mapper\AbstractEntityMapper;

class Mapper extends AbstractEntityMapper
{
	protected $_repositoryName = "Townspot\User\Entity";
	
	public function getIndexerRows($dateTime = null) 
	{
		$results = array();
		$sql  = "SELECT DISTINCT user.user_id,user.username,user.artist_name,user.email,user.city_id,user.province_id from user JOIN media on user.user_id = media.user_id";
		if ($dateTime) {
			$sql .= " WHERE user.updated >= '" . $dateTime->format('Y-m-d H:i:s') . "'";
		}
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}
}
