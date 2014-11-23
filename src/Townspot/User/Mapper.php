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

	public function getAdminList($options = array()) 
	{
		$where = array();
		$sort_field = 'user_id';
		$sort_order = 'ASC';
		$sql  = "SELECT user.user_id as id,
						user.username as username,
						user.display_name as name,
						user.email as email,
						CONCAT(city.name, ',', province.abbrev) as location,
						user.created as joined,
						user_role_linker.role_id as status
						from user 
						JOIN user_role_linker on user.user_id = user_role_linker.user_id
						LEFT JOIN city on user.city_id = city.id
						LEFT JOIN province on user.province_id = province.id";
		foreach ($options as $key => $value) {
			if ($value) {
				switch ($key) {
					case 'username':
						$where[] = "user.username LIKE '" . $value . "%'";
						break;
					case 'province':
						$where[] = "user.province_id = " . $value;
						break;
					case 'city':
						$where[] = "user.city_id = " . $value;
						break;
					case 'after':
						$where[] = "user.created >= '" . date('Y-m-d H:i:s',strtotime($value)) . "'";
						break;
					case 'before':
						$where[] = "user.created <= '" . date('Y-m-d H:i:s',strtotime($value)) . "'";
						break;
					case 'status':
						$where[] = "user_role_linker.role_id = '" . $value . "'";
						break;
					case 'type':
						if ($value == 'Administrator') {
							$where[] = "user_role_linker.role_id = 'Administrator'";
						} elseif ($value == 'Artist') {
							$where[] = "user_role_linker.role_id = 'Artist'";
						} 
						break;
					case 'sort_field':
						$sort_field = $value;
						break;
					case 'sort_order':
						$sort_order = $value;
						break;
				}
			}
		}
		if ($where) {
			$sql .= " WHERE " . implode(' AND ',$where);
		} 
		
		$sql .= " ORDER BY " . $sort_field . " " . $sort_order;
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}
	
	public function delete() 
	{
        $entity = $this->getEntity();
		$sql  = "DELETE from artist_comment where artist_comment.target_id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$sql  = "DELETE from artist_comment where artist_comment.user_id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$sql  = "DELETE from rating where rating.user_id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$sql  = "DELETE from user_activity where user_activity.user_id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$sql  = "DELETE from user_activity where user_activity.artist_id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$sql  = "DELETE from user_event where user_event.user_id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$sql  = "DELETE from user_favorite where user_favorite.user_id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$sql  = "DELETE from user_follow where user_follow.user_id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$sql  = "DELETE from user_follow where user_follow.target_id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$sql  = "DELETE from user_oauth where user_oauth.user_id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$sql  = "DELETE from user_role_linker where user_role_linker.user_id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$sql  = "DELETE from user_social_media where user_social_media.user_id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$seriesMapper = new \Townspot\Series\Mapper($this->getServiceLocator());
		foreach ($entity->getSeries as $series) {
			$seriesMapper->setEntity($series)->delete();
		}
		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		foreach ($entity->getMedia as $media) {
			$mediaMapper->setEntity($media)->delete();
		}
		$sql  = "DELETE from user where user.user_id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		
	}
}
