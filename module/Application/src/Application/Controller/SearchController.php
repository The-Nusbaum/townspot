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
		$results = array();
		$searchTerm = $this->params()->fromQuery('q');
		$page = 1;
		//City Search
		$locationIndex = new \Townspot\Lucene\LocationIndex($this->getServiceLocator());
		$query = new \ZendSearch\Lucene\Search\Query\MultiTerm();
		$query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'city_name'));
		$query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'province_name'));
		$results = array_merge($results,$locationIndex->find($query));
		//User Search
		$artistIndex = new \Townspot\Lucene\ArtistIndex($this->getServiceLocator());
		$query = new \ZendSearch\Lucene\Search\Query\MultiTerm();
		$query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'user_name'));
		$query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'artist_name'));
		$results = array_merge($results,$artistIndex->find($query));
		//Series Search
		$seriesIndex = new \Townspot\Lucene\SeriesIndex($this->getServiceLocator());
		$query = new \ZendSearch\Lucene\Search\Query\MultiTerm();
		$query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'series_name'));
		$results = array_merge($results,$seriesIndex->find($query));
		//Media Search
		$mediaIndex = new \Townspot\Lucene\VideoIndex($this->getServiceLocator());
		$query = new \ZendSearch\Lucene\Search\Query\MultiTerm();
		$query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'title'));
		$query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'logline'));
		$query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'description'));
		$results = array_merge($results,$mediaIndex->find($query));
		return new ViewModel(
			array(
				'searchTerm'    => $searchTerm,
				'page'    		=> $page,
				'matchesFound'	=> count($results),
			)
		);
    }
	
    public function resultsAction()
    {
		$searchTerm = $this->params()->fromPost('q');
		$page = $this->params()->fromPost('page');
		
    }
	
}
