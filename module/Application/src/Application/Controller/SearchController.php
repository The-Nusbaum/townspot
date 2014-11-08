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
        $results                    = array();
        $searchTerm                 = $this->params()->fromQuery('q');
        $sortTerm                   = ($this->params()->fromQuery('sort')) ?: 'created:desc';
        $page                       = $this->params()->fromQuery('page') ?: 1;
        $searchId                   = md5(serialize(array(session_id(),$searchTerm,$sortTerm)));
        $cache                      = $this->getServiceLocator()->get('cache-general');    
		$cache->clearExpired();
        list($sortField,$sortOrder) = explode(':',$sortTerm);
        $sortOrder                  = ($sortOrder == 'desc') ? SORT_DESC : SORT_ASC;

        // Clear Cache
        if ($results = $cache->getItem($searchId)) {
			$cache->removeItem($searchId);
		} 
		if ($results = $this->executeSearch($searchTerm, $sortField, $sortOrder)) {
			$cache->setItem($searchId, $results);
			return new ViewModel(
				array(
					'searchTerm'    => $searchTerm,
					'matchesFound'  => count($results),
					'sortTerm'  	=> $sortTerm,
					'searchId'  	=> $searchId,
					'page'  		=> $page,
				)
			);
		}
		return new ViewModel(
			array(
				'searchTerm'    => $searchTerm,
				'matchesFound'  => 0,
				'sortTerm'  	=> $sortTerm,
				'searchId'  	=> $searchId,
				'page'  		=> $page,
			)
		);
    }
    
    public function discoverAction()
    {
        $results        = array();
        $sortTerm       = ($this->params()->fromQuery('sort')) ?: 'created:desc';
        $page           = $this->params()->fromQuery('page') ?: 1;
        $cache          = $this->getServiceLocator()->get('cache-general');        
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
        $searchId                   = md5(serialize(array(session_id(),$terms,$sortTerm)));
        $cache                      = $this->getServiceLocator()->get('cache-general');        
        if ($results = $cache->getItem($searchId)) {
			$cache->removeItem($searchId);
		} 
		unset($_SESSION['DiscoverLocation']);
		unset($_SESSION['DiscoverLink']);
		unset($_SESSION['Discover_Province']);
		unset($_SESSION['Discover_City']);
		unset($_SESSION['Discover_Category']);
		unset($_SESSION['Discover_Categories']);
		unset($_SESSION['Discover_Subcategories']);

		$countryMapper  = new \Townspot\Country\Mapper($this->getServiceLocator());
		$provinceMapper = new \Townspot\Province\Mapper($this->getServiceLocator());
		$cityMapper 	= new \Townspot\City\Mapper($this->getServiceLocator());
		$categoryMapper = new \Townspot\Category\Mapper($this->getServiceLocator());
		$country  		= $countryMapper->findOneByName('United States');

		//Has State?
		$provinceId		= null;
		$provinceName	= null;
		$cityId			= null;
		$cityName		= null;
		$prefix			= null;
		$categories		= array();
		foreach ($terms as $index => $term) {
			if ($provinces = $provinceMapper->findByName($term)) {
				foreach ($provinces as $province) {
					if ($province->getCountry()->getId() == $country->getId()) {
						if (strtolower($province->getName()) == strtolower($term)) {
							$provinceId 	= $province->getId();
							$prefix 		= $province->getDiscoverLink();
							$provinceName 	= $term;
							$_SESSION['Discover_Province'] = $provinceId;
							$_SESSION['DiscoverLocation'] = $province;
							$_SESSION['DiscoverLink'] = $province->getDiscoverLink();
							unset($terms[$index]);
						}
					}
				}
			}
		}
		if ($provinceId) {
			foreach ($terms as $index => $term) {
				if ($cities = $cityMapper->findByName($term)) {
					foreach ($cities as $city) {
						if ($city->getProvince()->getId() == $provinceId) {
							$cityId 	= $city->getId();
							$cityName 	= $term;
							$prefix 	= $city->getDiscoverLink();
							$_SESSION['Discover_City'] = $cityId;
							$_SESSION['DiscoverLocation'] = $city;
							$_SESSION['DiscoverLink'] = $city->getDiscoverLink();
							unset($terms[$index]);
						}
					}
				}
			}
		}
		$activeCategory = null;
		if (in_array('all videos',$terms)) {
			$activeCategory = -1;
			$_SESSION['Discover_Categories'] = array(
				array(
					'name' => 'all videos')
				);
		} else {
			if ($terms) {
				$terms = $categoryMapper->findFromArray($terms);
				$selectedCategories = $terms;
				$activeCategory = array_pop($terms);
				$activeCategory = $activeCategory['id'];
				$subcategories = $categoryMapper->findChildrenIdAndName($activeCategory,$provinceId,$cityId);
				$_SESSION['Discover_Categories'] = $selectedCategories;
				$_SESSION['Discover_Subcategories'] = $subcategories;
			} 
		}
		
		if ($results = $this->executeDiscoverSearch($provinceId, $cityId, $activeCategory,$sortTerm)) {
			$cache->setItem($searchId, $results);
			return new ViewModel(
				array(
					'page'  	 	=> 1,
					'searchId'  	=> $searchId,
					'province' 		=> $provinceName,
					'city'  	 	=> $cityName,
					'categoryId' 	=> $activeCategory,
					'matchesFound'  => count($results),
					'sortTerm'  	=> $sortTerm,
				)
			);
		} else {
			return new ViewModel(
				array(
					'page'  	 	=> 1,
					'searchId'  	=> $searchId,
					'province' 		=> $provinceName,
					'city'  	 	=> $cityName,
					'categoryId' 	=> $activeCategory,
					'matchesFound'  => 0,
					'sortTerm'  	=> $sortTerm,
				)
			);
		}
    }

    protected function executeSearch($searchTerm, $sortField, $sortOrder)
    {
        $cache                      = $this->getServiceLocator()->get('cache-general');        
		$seriesMapper 			= new \Townspot\Series\Mapper($this->getServiceLocator());

        \ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(
            new \ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive()
        );
		$results = array();
		
        //City Search
        $locationIndex              = new \Townspot\Lucene\LocationIndex($this->getServiceLocator());
        $query                      = new \ZendSearch\Lucene\Search\Query\MultiTerm();
        $query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'city'));
        $query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'province'));
        $matches                    = $locationIndex->find($query,'city',SORT_STRING,$sortOrder);
        foreach ($matches as $hit) {
            $results[] = array(
                'type'  => 'city',
                'id'    => $hit->objectid,
            );
        }

        //User Search
        $artistIndex                = new \Townspot\Lucene\ArtistIndex($this->getServiceLocator());
        $query                      = new \ZendSearch\Lucene\Search\Query\MultiTerm();
        $query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'username'));
        $query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'artist_name'));
        if ($sortField == 'created') {
            $matches = $artistIndex->find($query,'created',SORT_NUMERIC,$sortOrder);
        } else {
            $matches = $artistIndex->find($query,'username',SORT_STRING,$sortOrder);
        }
        foreach ($matches as $hit) {    
            $results[] = array(
                'type'  => 'artist',
                'id'    => $hit->objectid,
            );
        }

        //Series Search
        $seriesIndex                = new \Townspot\Lucene\SeriesIndex($this->getServiceLocator());
        $query                      = new \ZendSearch\Lucene\Search\Query\MultiTerm();
        $query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'name'));
        $query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'description'));
        $query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'media_titles'));
        $query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'media_descriptions'));
        $query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'media_loglines'));
        if ($sortField == 'created') {
            $matches = $seriesIndex->find($query,'created',SORT_NUMERIC,$sortOrder);
        } else {
            $matches = $seriesIndex->find($query,'name',SORT_STRING,$sortOrder);
        }
        foreach ($matches as $hit) {    
//            $results[] = array(
//                'type'  => 'series',
//                'id'    => $hit->objectid,
//            );
			$series = $seriesMapper->find($hit->objectid);
			$episodes = $series->getEpisodes();
			if ($sortOrder == SORT_DESC) {
				$_episodes = array();
				foreach ($episodes as $episode) {
					$_episodes[] = $episode;
				}
				$episodes = array_reverse($_episodes);
			}
			foreach ($episodes as $episode) {
				$results[] = array(
					'type'  => 'media',
					'id'    => $episode->getMedia()->getId(),
				);
			}
        }
		
        //Media Search
        $mediaIndex                 = new \Townspot\Lucene\VideoIndex($this->getServiceLocator());
        $query                      = new \ZendSearch\Lucene\Search\Query\MultiTerm();
        $query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'title'));
        $query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'logline'));
        $query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'description'));
        $query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'series_name'));
        if ($sortField == 'created') {
            $matches = $mediaIndex->getIndex()
  		                          ->find($query,'created',SORT_NUMERIC,$sortOrder);
        } elseif ($sortField == 'views') {
            $matches = $mediaIndex->getIndex()
			                      ->find($query,'views',SORT_NUMERIC,$sortOrder);
        } else {
            $matches = $mediaIndex->getIndex()
			                      ->find($query,'title',SORT_STRING,$sortOrder);
        }

        foreach ($matches as $hit) {   
			$results[] = array(
				'type'  => 'media',
				'id'    => $hit->objectid,
			);
        }
		
		return $results;
	}
	
    protected function executeDiscoverSearch($province_id, $city_id, $category_id,$sort)
    {
		$results 		= array();
		if ($category_id) {
			$mediaMapper 	= new \Townspot\Media\Mapper($this->getServiceLocator());
			if ($category_id < 0) {
				$category_id = null;
			}
			$matches = $mediaMapper->getDiscoverMedia($province_id,$city_id,$category_id,$sort);
			foreach ($matches as $match) {    
				$results[] = array(
					'type'  => 'media',
					'id'    => $match['id'],
				);
			}
		} else {
			$categoryMapper 	= new \Townspot\Category\Mapper($this->getServiceLocator());
			$matches = $categoryMapper->getDiscoverCategories($province_id,$city_id);
			foreach ($matches as $match) {    
				$results[] = array(
					'type'  => 'category',
					'id'    => $match->getId(),
				);
			}
		}
		return $results;
	}
	
}
