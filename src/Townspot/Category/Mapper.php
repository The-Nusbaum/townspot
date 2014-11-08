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
		$other = null;
		foreach ($_results as $result) {
			$category = $this->find($result['id']);
			if (strtolower($category->getName()) == 'other') {
				$other = $category;
			} else {
				$results[] = $this->find($result['id']);
			}
		}
		if ($other) {
			$results[] = $other;
		}
		return $results;
	}
	
	public function findFromArray($categories) 
	{
		$results = array();
		if (!is_array($categories)) {
			$categories = array(categories);
		}
		$parent = null;
		while ($categories) {
			$category = array_shift($categories);
			$_category = $this->findOneBy(
				array(
					'_name'		=> $category,
					'_parent'	=> $parent,
				)
			);
			if ($_category) {
				$results[] = array(
					'id'	=> $_category->getId(),
					'name'	=> $_category->getName()
				);
				$parent = $_category->getId();
			}
		}
		return $results;
	}
	
	public function findChildrenIdAndName($parent = null,$province_id = null,$city_id = null) 
	{
		$results 		= array();
		$categories = $this->getDiscoverCategories($province_id,$city_id,$parent);
		foreach ($categories as $category) {
			$results[] = array(
				'id'	=> $category->getId(),
				'name'	=> $category->getName()
			);
		}
		return $results;
	}
	
	public function findRandom($province_id = null,$city_id = null) 
	{
		$results 		= array();
		$categories = $this->getDiscoverCategories($province_id,$city_id);
		$randkey = rand(0,count($categories));
		return $categories[$randkey];
	}
	
}
