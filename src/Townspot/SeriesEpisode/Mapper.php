<?php
namespace Townspot\SeriesEpisode;
use Townspot\Doctrine\Mapper\AbstractEntityMapper;

class Mapper extends AbstractEntityMapper
{
	protected $_repositoryName = "Townspot\SeriesEpisode\Entity";
	
	public function delete() 
	{
        $entity = $this->getEntity();
		$sql  = "DELETE from series_episodes where series_episodes.id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
	}
}
