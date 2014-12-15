<?php
namespace Townspot\UserFollowing;
use Townspot\Doctrine\Mapper\AbstractEntityMapper;

class Mapper extends AbstractEntityMapper
{
	protected $_repositoryName = "Townspot\UserFollowing\Entity";
	
	public function delete() 
	{
        $entity = $this->getEntity();
		$sql  = "DELETE from user_follow where user_follow.id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
	}
	
}
