<?php
namespace Townspot\ArtistComment;
use Townspot\Doctrine\Mapper\AbstractEntityMapper;

class Mapper extends AbstractEntityMapper
{
	protected $_repositoryName = "Townspot\ArtistComment\Entity";

	public function delete() 
	{
        $entity = $this->getEntity();
		$sql  = "DELETE from artist_comment where artist_comment.id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
	}
}
