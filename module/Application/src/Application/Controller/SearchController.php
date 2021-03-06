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
		$this->getServiceLocator()
			 ->get('ViewHelperManager')
			 ->get('HeadMeta')
			 ->appendProperty('og:title', 'Townspot.tv')
			 ->appendProperty('og:description', 'TownSpot.tv is the Local Video Network spotlighting local talent across the country through a curated video directory.')
			 ->appendProperty('og:site_name', 'townspot.tv')
			 ->appendProperty('og:url', 'http://www.townspot.tv/')
			 ->appendProperty('og:image', 'http://www.townspot.tv/img/townspotwhat.jpg')
			 ->appendProperty('twitter:card', 'summary')
			 ->appendProperty('twitter:title','Townspot.tv')
			 ->appendProperty('twitter:description', 'TownSpot.tv is the Local Video Network spotlighting local talent across the country through a curated video directory.')
			 ->appendProperty('twitter:image', 'http://www.townspot.tv/img/townspotwhat.jpg');
    }

    public function indexAction()
    {
		$this->init();
		$searchTerm = $this->params()->fromQuery('q');
        $sortTerm   = ($this->params()->fromQuery('sort')) ?: 'relevance:desc';
        $page       = $this->params()->fromQuery('page') ?: 1;
        $searchId   = md5(serialize(array(time(),session_id(),$searchTerm,$sortTerm)));
        list($sortField,$sortOrder) = explode(':',$sortTerm);
        $sortOrder  = ($sortOrder == 'desc') ? SORT_DESC : SORT_ASC;
		$searchTerm = strtolower($searchTerm);	
		$searchTerm = preg_replace('/[^a-z0-9 -]+/', '', $searchTerm);		

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
        unset($_SESSION['Discover_Country']);
		unset($_SESSION['Discover_Province']);
		unset($_SESSION['Discover_City']);
		unset($_SESSION['Discover_Category']);
		unset($_SESSION['Discover_Categories']);
		unset($_SESSION['Discover_Subcategories']);
		
		$search		= new \Townspot\Search\Search($this->getServiceLocator());
		$results	= $search->discoverSearch($terms,$sortTerm,$page);
        $countryMapper = new \Townspot\country\Mapper($this->getServiceLocator());
		$provinceMapper = new \Townspot\Province\Mapper($this->getServiceLocator());
		$cityMapper 	= new \Townspot\City\Mapper($this->getServiceLocator());
        if ($results['countryId']) {
            $_SESSION['Discover_Country'] = $results['countryId'];
            $_SESSION['DiscoverLocation'] = $countryMapper->find($results['countryId']);
        }
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
                'country'      => $results['countryName'],
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

    protected function _getChildren($cats) {
        $categories = array();
        foreach($cats as $c) {
            $categories[$c->getId()] = array(
                'name' => $c->getName(),
            );
            if($c->getCategories()) {
                $subCats = $c->getCategories();
                foreach($subCats as $sc){
                    $categories[$c->getId()]['children'] = $this->_getChildren(array());
                }
            }
        }
        return $categories;
    }

    public function channelsurfAction() {
        $this->init();

        $categoryMapper = new \Townspot\Category\Mapper($this->getServiceLocator());
        $countryMapper = new \Townspot\Country\Mapper($this->getServiceLocator());
        $provinceMapper = new \Townspot\Province\Mapper($this->getServiceLocator());
        $cityMapper = new \Townspot\City\Mapper($this->getServiceLocator());

        $_country = $this->params()->fromRoute('country');
        $_country = preg_replace('/[_]/',' ',$_country);
        $_state = $this->params()->fromRoute('state');
        $_state = preg_replace('/[_]/',' ',$_state);
        $_city = $this->params()->fromRoute('city');
        $_city = preg_replace('/[_]/',' ',$_city);
        $cat1 = $this->params()->fromRoute('cat1');
        $cat2 = $this->params()->fromRoute('cat2');
        $cat3 = $this->params()->fromRoute('cat3');
        $cat4 = $this->params()->fromRoute('cat4');
        $cat5 = $this->params()->fromRoute('cat5');

        if($_country != 'all-countries') {
            $country = $countryMapper->findOneByName($_country)->getId();
        } else {
            $country = null;
        }

        if($_state != 'all-states') {
            $state = $provinceMapper->findOneBy(array(
                    '_name' => str_replace('_',' ',$_state),
                    '_country' => $country
            ))->getId();
        } else {
            $state = null;
        }

        if($_city != 'all-cities') {
            $city = $cityMapper->findOneBy(array(
                '_name' => str_replace('_',' ',$_city),
                '_province' => $state
            ))->getId();
        } else {
            $city = null;
        }

        $category = null;
        for($i = 1; $i <= 5; $i++) {
            $query = $categoryMapper;
            $var = "cat$i";
            if($$var != null) {
                $$var = preg_replace('/[_]/', ' ', $$var);
                $$var = preg_replace('/[+]/', '/', $$var);
                if ($i == 1) $params = array('_name' => $$var);
                else $params = array(
                    '_parent' => $category,
                    '_name' => ucwords($$var),
                );
                $category = $categoryMapper->findOneBy($params);
            }
        }
        //var_dump($category);
        if(empty($category))

        $this->getServiceLocator()
            ->get('ViewHelperManager')
            ->get('HeadScript')
            ->appendFile('/js/videointeractions.js','text/javascript');

        $countries = $countryMapper->getCountriesHavingMedia();
        $states = array();
        foreach($countries as $c){
            $states[$c['id']] = $provinceMapper->getProvincesHavingMedia($c['name']);
        }
        $cities = array();
        foreach($states as $country_id => $_states) foreach($_states as $s){
            $cities[$s['id']] = $cityMapper->getCitiesHavingMedia($s['id']);
        }
        $categories = $categoryMapper->getTreeBranches();
        $flatCats = array();
        foreach($categoryMapper->findAll() as $cat) {
            $flatCats[$cat->getId()] = $cat->getName();
        }

        return new ViewModel(
            array(
                'countries'     => $countries,
                'states'        => $states,
                'cities'        => $cities,
                'categories'    => $categories,
                'country'       => $country,
                'state'  		=> $state,
                'city'          => $city,
                'category'      => $category,
                'flatCats'      => $flatCats
            )
        );
    }
}
