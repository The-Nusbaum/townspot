<?php
namespace Townspot\Doctrine\Mapper;

interface EntityMapperInterface
{
	public function save();
	public function delete();
	public function setEntity($entity);
	public function getEntity();
	public function setEntityManager(\Doctrine\ORM\EntityManager $entityManager);
	public function getEntityManager();
	public function setRepository(\Doctrine\ORM\EntityRepository $repository);
	public function getRepository();
}
