<?php
namespace Townspot\UserEvent;
use Townspot\Doctrine\Mapper\AbstractEntityMapper;

class Mapper extends AbstractEntityMapper
{
	protected $_repositoryName = "Townspot\UserEvent\Entity";
	
	public function delete() 
	{
        $entity = $this->getEntity();
		$sql  = "DELETE from user_event where user_event.id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
	}
	
}
