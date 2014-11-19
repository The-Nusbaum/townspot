<?php
namespace Townspot\User;
use Townspot\Doctrine\Mapper\AbstractEntityMapper;

class Mapper extends AbstractEntityMapper
{
	protected $_repositoryName = "Townspot\User\Entity";
	
	public function getIndexerRows($dateTime = null) 
	{
		$results = array();
		$sql  = "SELECT DISTINCT user.user_id,
							     user.username,
								 user.artist_name,
								 user.email,
								 city.name as city,
								 province.name as province
				 FROM user 
				 JOIN media on user.user_id = media.user_id
				 JOIN province on user.province_id = province.id
				 JOIN city on user.city_id = city.id
			     WHERE media.approved = 1 ";
		if ($dateTime) {
			$sql .= " AND (media.updated >= '" . $dateTime->format('Y-m-d H:i:s') . "'";
			$sql .= " OR user.updated >= '" . $dateTime->format('Y-m-d H:i:s') . "')";
		}
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}
	
	public function getStats() 
	{
		$sql  = "SELECT count(*) as user_count ";
		$sql .= "FROM tsz.user";
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		return $stmt->fetch();
	}

	public function getTopArtistStats($count = 10) 
	{
		$sql  = "SELECT media.user_id,user.username,count(media.id) as video_count from media ";
		$sql .= "JOIN user on media.user_id = user.user_id ";
		$sql .= "GROUP BY user_id ";
		$sql .= "ORDER BY video_count DESC ";
		$sql .= "LIMIT " . $count;
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}
	
	public function getTopCommenterStats($count = 10) 
	{
		$sql  = "SELECT media_comment.user_id,user.username,count(media_comment.id) as comment_count from media_comment ";
		$sql .= "JOIN user on media_comment.user_id = user.user_id ";
		$sql .= "GROUP BY user_id ";
		$sql .= "ORDER BY comment_count DESC ";
		$sql .= "LIMIT " . $count;
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}
	
	public function findByType($type = null) 
	{
		$results = array();
		$sql  = "SELECT user.user_id as id FROM tsz.user ";
		$sql .= "JOIN user_role_linker on user.user_id = user_role_linker.user_id ";
		$sql .= "WHERE user_role_linker.role_id='" . $type . "'";
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		if ($_results = $stmt->fetchAll()) {
			foreach ($_results as $result) {
				if ($user = $this->find($result['id'])) {
					$results[] = $user;
				}
			}
		}
		return $results;
	}
	
	public function usernameTypeahead($value = null,$limit = 10) 
	{
		$results = array();
		$sql  = "SELECT DISTINCT user.username
				 FROM user 
			     WHERE user.username LIKE '" . $value . "%'
				 LIMIT " . $limit;
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$_results = $stmt->fetchAll();
		foreach ($_results as $result) {
			$value = array_shift($result);
			$results[] = array('val'	=> $value);
		}
		return $results;
	}
	
}
