<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SearchController extends AbstractActionController
{
    public function __construct() 
    {
    }
    
    public function init() 
    {
        $this->getServiceLocator()
             ->get('ViewHelperManager')
             ->get('HeadTitle')
             ->set('TownSpot &bull; Your Town. Your Talent. Spotlighted');
    }

    public function indexAction()
    {
		$this->init();
		$searchTerm = $this->params()->fromQuery('q');
        $sortTerm   = ($this->params()->fromQuery('sort')) ?: 'created:desc';
        $page       = $this->params()->fromQuery('page') ?: 1;
        $searchId   = md5(serialize(array(time(),session_id(),$searchTerm,$sortTerm)));
        list($sortField,$sortOrder) = explode(':',$sortTerm);
        $sortOrder  = ($sortOrder == 'desc') ? SORT_DESC : SORT_ASC;

		$search		= new \Townspot\Search\Search($this->getServiceLocator());
		$results    = $search->keywordSearch($searchTerm, $sortField, $sortOrder,$page);
		return new ViewModel(
			array(
				'searchTerm'    => $searchTerm,
				'matchesFound'  => $results['matchesFound'],
				'sortTerm'  	=> $sortTerm,
				'searchId'  	=> $results['searchId'],
				'data'  		=> json_encode($results['data']),
				'page'  		=> $page,
			)
		);
    }
    
    public function discoverAction()
    {
		$this->init();
        $results        = array();
        $sortTerm       = ($this->params()->fromQuery('sort')) ?: 'created:desc';
        $page           = $this->params()->fromQuery('page') ?: 1;
        $terms = array();
        if ($term = $this->params()->fromRoute('param1'))       {   $terms[] = $term;   }
        if ($term = $this->params()->fromRoute('param2'))       {   $terms[] = $term;   }
        if ($term = $this->params()->fromRoute('param3'))       {   $terms[] = $term;   }
        if ($term = $this->params()->fromRoute('param4'))       {   $terms[] = $term;   }
        if ($term = $this->params()->fromRoute('param5'))       {   $terms[] = $term;   }
        if ($term = $this->params()->fromRoute('param6'))       {   $terms[] = $term;   }
        if ($term = $this->params()->fromRoute('param7'))       {   $terms[] = $term;   }
        if ($term = $this->params()->fromRoute('param8'))       {   $terms[] = $term;   }
        if ($term = $this->params()->fromRoute('param9'))       {   $terms[] = $term;   }
        if ($term = $this->params()->fromRoute('param10'))      {   $terms[] = $term;   }
		$_terms = $terms;
		
		unset($_SESSION['DiscoverLocation']);
		unset($_SESSION['DiscoverLink']);
		unset($_SESSION['Discover_Province']);
		unset($_SESSION['Discover_City']);
		unset($_SESSION['Discover_Category']);
		unset($_SESSION['Discover_Categories']);
		unset($_SESSION['Discover_Subcategories']);
		
		$search		= new \Townspot\Search\Search($this->getServiceLocator());
		$results	= $search->discoverSearch($terms,$sortTerm,$page);
		$provinceMapper = new \Townspot\Province\Mapper($this->getServiceLocator());
		$cityMapper 	= new \Townspot\City\Mapper($this->getServiceLocator());
		if ($results['provinceId']) {
			$_SESSION['Discover_Province'] = $results['provinceId'];
			$_SESSION['DiscoverLocation'] = $provinceMapper->find($results['provinceId']);
		}
		if ($results['cityId']) {
			$_SESSION['Discover_City'] = $results['cityId'];
			$_SESSION['DiscoverLocation'] = $cityMapper->find($results['cityId']);
		}
		if ($results['prefix']) {
			$_SESSION['DiscoverLink'] = $results['prefix'];
		}
		if ($results['activeCategory'] < 0) {
			$_SESSION['Discover_Categories'] = array(	
				array(
					'name' => 'all videos'	
				)
			);
		} else {
			$_SESSION['Discover_Categories'] = $results['categories'];
			$_SESSION['Discover_Subcategories'] = $results['subcategories'];
		}
		return new ViewModel(
			array(
				'page'  	 	=> 1,
				'searchId'  	=> $results['searchId'],
				'province'  	=> $results['provinceName'],
				'city'  		=> $results['cityName'],
				'categoryId'  	=> $results['activeCategory'],
				'matchesFound'  => $results['matchesFound'],
				'sortTerm'  	=> $sortTerm,
				'terms'  		=> json_encode($_terms),
				'data'  		=> json_encode($results['data']),
			)
		);
    }
}
