<?php
namespace Townspot\Category;
use Townspot\Doctrine\Mapper\AbstractEntityMapper;

class Mapper extends AbstractEntityMapper
{
	protected $_repositoryName = "Townspot\Category\Entity";
	
	public function getDiscoverCategories($province_id = null,$city_id = null,$parent = null) 
	{
		$results 		= array();
		$provinceMapper = new \Townspot\Province\Mapper($this->getServiceLocator());
		$cityMapper = new \Townspot\City\Mapper($this->getServiceLocator());
		$province = ($province_id) ? $provinceMapper->find($province_id) : null;
		$city = ($city_id) ? $cityMapper->find($city_id) : null;
		$sql  = "SELECT ";
		$sql .= " DISTINCT category.id ";
		$sql .= "FROM  ";
		$sql .= " category";
		$sql .= " JOIN media_category_linker on media_category_linker.category_id = category.id ";
		$sql .= " JOIN media on media_category_linker.media_id = media.id ";
		$where = array('media.approved = 1');
		$having = array();
		if ($province_id) {
			$where[] = 'media.province_id=' . $province_id;
		}
		if ($city_id) {
			$where[] = 'media.city_id=' . $city_id;
		}
		if ($parent) {
			$where[] = 'category.parent_id=' . $parent;
		} else {
			$where[] = 'category.parent_id IS NULL';
		}
		if ($where) {
			$sql .= " WHERE " . implode(' AND ',$where);
		}
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$_results = $stmt->fetchAll();
		foreach ($_results as $result) {
			$results[] = $this->find($result['id']);
		}
		return $results;
	}
}
