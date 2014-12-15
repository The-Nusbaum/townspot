<?php
namespace Townspot\MediaComment;
use Townspot\Doctrine\Mapper\AbstractEntityMapper;

class Mapper extends AbstractEntityMapper
{
	protected $_repositoryName = "Townspot\MediaComment\Entity";
	
	public function findByMediaId($id,$page=1,$limit=100) 
	{
		$results = array();
		$min_limit = ($page - 1) * $limit;
		$max_limit = $min_limit + $limit;
		$sql  = "SELECT id FROM media_comment WHERE target_id=%d ORDER BY created DESC LIMIT %d,%d";
		$sql  = sprintf($sql,$id,$min_limit,$max_limit);
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		if ($records = $stmt->fetchAll()) {
			foreach ($records as $record) {
				$results[] = $this->find($record['id']);
			}
		}
		return $results;
	}

	public function delete() 
	{
        $entity = $this->getEntity();
		$sql  = "DELETE from media_comment where media_comment.id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
	}
	
}
