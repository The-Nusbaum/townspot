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
        $sortTerm                   = ($this->params()->fromQuery('sort')) ?: 'date:asc';
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
    }

    protected function executeSearch($searchTerm, $sortField, $sortOrder)
    {
        $cache                      = $this->getServiceLocator()->get('cache-general');        
        $sortOrder                  = ($sortOrder == 'desc') ? SORT_DESC : SORT_ASC;

        \ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(
            new \ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive()
        );

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
        if ($sortField == 'date') {
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
        $query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'media_descriptions'));
        $query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'media_titles'));
        $query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'media_loglines'));
        if ($sortField == 'date') {
            $matches = $seriesIndex->find($query,'created',SORT_NUMERIC,$sortOrder);
        } else {
            $matches = $seriesIndex->find($query,'name',SORT_STRING,$sortOrder);
        }
        foreach ($matches as $hit) {    
            $results[] = array(
                'type'  => 'series',
                'id'    => $hit->objectid,
            );
        }

        //Media Search
        $mediaIndex                 = new \Townspot\Lucene\VideoIndex($this->getServiceLocator());
        $query                      = new \ZendSearch\Lucene\Search\Query\MultiTerm();
        $query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'title'));
        $query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'logline'));
        $query->addTerm(new \ZendSearch\Lucene\Index\Term($searchTerm, 'description'));
        if ($sortField == 'date') {
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
}
