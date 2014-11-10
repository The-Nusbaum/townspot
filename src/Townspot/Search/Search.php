<?php
namespace Townspot\Search;

use Townspot\Lucene\LocationIndex;
use Townspot\Lucene\ArtistIndex;
use Townspot\Lucene\SeriesIndex;
use Townspot\Lucene\VideoIndex;
use \ZendSearch\Lucene\Search\Query\MultiTerm;
use \ZendSearch\Lucene\Index\Term;
use Zend\ServiceManager\ServiceLocatorAwareInterface,
    Zend\ServiceManager\ServiceLocatorInterface;
	
class Search implements ServiceLocatorAwareInterface
{  
    public function __construct(ServiceLocatorInterface $serviceLocator) 
	{
		$this->setServiceLocator($serviceLocator);
	}

    public function keywordSearch($keyword, $sortField, $sortOrder,$page = 1)
    {
        $searchId       = md5(serialize(array($keyword,$sortField,$sortOrder)));
        $cache			= $this->getServiceLocator()->get('cache-general');        
		$cache->clearExpired();
        $results 		= $cache->getItem($searchId);

		if (!$results) {
			$cityMapper 	= new \Townspot\City\Mapper($this->getServiceLocator());
			$seriesMapper 	= new \Townspot\Series\Mapper($this->getServiceLocator());
			$userMapper 	= new \Townspot\User\Mapper($this->getServiceLocator());
			$mediaMapper 	= new \Townspot\Media\Mapper($this->getServiceLocator());
		
			\ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(
				new \ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive()
			);
			//City Search
			$locationIndex = new LocationIndex($this->getServiceLocator());
			$query         = new MultiTerm();
			$query->addTerm(new Term($keyword, 'city'));
			$query->addTerm(new Term($keyword, 'province'));
			$matches       = $locationIndex->find($query,'city',SORT_STRING,$sortOrder);
			foreach ($matches as $hit) {
				$city = $cityMapper->find($hit->objectid);
				$media = $city->getRandomMedia();
				$data[] = array(
					'id'				=> $city->getId(),
					'type'				=> 'city',
					'link'				=> $city->getDiscoverLink(),
					'image'				=> $media->getResizerCdnLink(),
					'escaped_title'		=> $city->getFullName(),
					'title'				=> $city->getFullName(),
					'location'			=> $city->getFullName(),
					'escaped_location'	=> $city->getFullName(),
				);
			}
			//Artist Search
			$artistIndex   = new ArtistIndex($this->getServiceLocator());
			$query         = new MultiTerm();
			$query->addTerm(new Term($keyword, 'username'));
			$query->addTerm(new Term($keyword, 'artist_name'));
			if ($sortField == 'created') {
				$matches = $artistIndex->find($query,'created',SORT_NUMERIC,$sortOrder);
			} else {
				$matches = $artistIndex->find($query,'username',SORT_STRING,$sortOrder);
			}
			foreach ($matches as $hit) {
				$user = $userMapper->find($hit->objectid);
				$media = $user->getRandomMedia();
				$data[] = array(
					'id'				=> $user->getId(),
					'type'				=> 'user',
					'link'				=> $user->getProfileLink(),
					'image'				=> $media->getResizerCdnLink(),
					'escaped_title'		=> $user->getUsername(true),
					'title'				=> $user->getUsername(),
					'user'				=> $user->getUsername(),
					'user_profile'		=> $user->getProfileLink(),
					'location'			=> $media->getLocation(),
					'escaped_location'	=> $media->getLocation(false,true),
				);
			}
			//Artist Search
			$seriesIndex   = new SeriesIndex($this->getServiceLocator());
			$query         = new MultiTerm();
			$query->addTerm(new Term($keyword, 'name'));
			$query->addTerm(new Term($keyword, 'description'));
			$query->addTerm(new Term($keyword, 'media_titles'));
			$query->addTerm(new Term($keyword, 'media_descriptions'));
			$query->addTerm(new Term($keyword, 'media_loglines'));
			if ($sortField == 'created') {
				$matches = $seriesIndex->find($query,'created',SORT_NUMERIC,$sortOrder);
			} else {
				$matches = $seriesIndex->find($query,'name',SORT_STRING,$sortOrder);
			}
			foreach ($matches as $hit) {
				$series = $seriesMapper->find($hit->objectid);
				$media = $series->getRandomMedia();
				$data[] = array(
					'id'				=> $series->getId(),
					'type'				=> 'series',
					'link'				=> $series->getSeriesLink(),
					'image'				=> $media->getResizerCdnLink(),
					'escaped_title'		=> $series->getName(),
					'title'				=> $series->getName(),
					'user'				=> $series->getUser()->getUsername(),
					'user_profile'		=> $series->getUser()->getProfileLink(),
					'location'			=> $media->getLocation(),
					'escaped_location'	=> $media->getLocation(false,true),
					'series_name'		=> $series->getName(),
					'series_link'		=> $series->getSeriesLink(),
				);
			}
			$mediaIndex    = new VideoIndex($this->getServiceLocator());
			$query         = new MultiTerm();
			$query->addTerm(new Term($keyword, 'title'));
			$query->addTerm(new Term($keyword, 'logline'));
			$query->addTerm(new Term($keyword, 'description'));
			$query->addTerm(new Term($keyword, 'series_name'));
			if ($sortField == 'created') {
				$matches = $mediaIndex->getIndex()->find($query,'created',SORT_NUMERIC,$sortOrder);
			} elseif ($sortField == 'views') {
				$matches = $mediaIndex->getIndex()->find($query,'views',SORT_NUMERIC,$sortOrder);
			} else {
				$matches = $mediaIndex->getIndex()->find($query,'title',SORT_STRING,$sortOrder);
			}
			foreach ($matches as $hit) {   
				$media = $mediaMapper->find($hit->objectid);
				$added = false;
				if ($episodes = $media->getEpisode()) {
					if ($episode = $episodes[0]) {
						if ($series = $episode->getSeries()) {
							$data[] = array(
								'id'				=> $media->getId(),
								'type'				=> 'media',
								'link'				=> $media->getMediaLink(),
								'image'				=> $media->getResizerCdnLink(),
								'escaped_title'		=> $media->getTitle(false,true),
								'title'				=> $media->getTitle(),
								'logline'			=> $media->getLogline(),
								'escaped_logline'	=> $media->getLogline(true),
								'user'				=> $media->getUser()->getUsername(),
								'user_profile'		=> $media->getUser()->getProfileLink(),
								'duration'			=> $media->getDuration(true),
								'comment_count'		=> count($media->getCommentsAbout()),
								'views'				=> $media->getViews(),
								'location'			=> $media->getLocation(),
								'escaped_location'	=> $media->getLocation(false,true),
								'rate_up'			=> count($media->getRatings(true)),
								'rate_down'			=> count($media->getRatings(false)),
								'series_name'		=> $series->getName(),
								'series_link'		=> $series->getSeriesLink(),
							);
							$added = true;
						}
					}
				}
				if (!$added) {	
					$data[] = array(
						'id'				=> $media->getId(),
						'type'				=> 'media',
						'link'				=> $media->getMediaLink(),
						'image'				=> $media->getResizerCdnLink(),
						'escaped_title'		=> $media->getTitle(false,true),
						'title'				=> $media->getTitle(),
						'logline'			=> $media->getLogline(),
						'escaped_logline'	=> $media->getLogline(true),
						'user'				=> $media->getUser()->getUsername(),
						'user_profile'		=> $media->getUser()->getProfileLink(),
						'duration'			=> $media->getDuration(true),
						'comment_count'		=> count($media->getCommentsAbout()),
						'views'				=> $media->getViews(),
						'location'			=> $media->getLocation(),
						'escaped_location'	=> $media->getLocation(false,true),
						'rate_up'			=> count($media->getRatings(true)),
						'rate_down'			=> count($media->getRatings(false)),
					);
				}
			}
			$results = array(
				'searchId' 		=> $searchId,
				'matchesFound' 	=> count($data),
				'data'			=> $data
			);
			$cache->setItem($searchId, $results);
		}
		$startRange = ($page - 1) * 11;
		$results['data'] = array_slice($results['data'],$startRange,11);
		if (count($results['data']) > 6) {
			$ad = array(
				array(
					'type'				=> 'ad',
					'title'				=> 'search',
				)
			);
			array_splice( $results['data'], 7, 0, $ad );
		}
		return $results;
	}

    public function discoverSearch($terms,$sortTerm, $page = 1)
    {
        $searchId       = md5(serialize(array($terms,$sortTerm,$page)));
        $cache			= $this->getServiceLocator()->get('cache-general');        
		$cache->clearExpired();
        $results 		= $cache->getItem($searchId);
		if (!$results) {
			$provinceId		= null;
			$provinceName	= null;
			$cityId			= null;
			$cityName		= null;
			$prefix			= null;
			$categories		= array();
			$subcategories  = array();
			$countryMapper  = new \Townspot\Country\Mapper($this->getServiceLocator());
			$provinceMapper = new \Townspot\Province\Mapper($this->getServiceLocator());
			$cityMapper 	= new \Townspot\City\Mapper($this->getServiceLocator());
			$categoryMapper = new \Townspot\Category\Mapper($this->getServiceLocator());
			$country  		= $countryMapper->findOneByName('United States');
			if ($terms) {
				foreach ($terms as $index => $term) {
					if ($provinces = $provinceMapper->findByName($term)) {
						foreach ($provinces as $province) {
							if ($province->getCountry()->getId() == $country->getId()) {
								if (strtolower($province->getName()) == strtolower($term)) {
									$provinceId 	= $province->getId();
									$prefix 		= $province->getDiscoverLink();
									$provinceName 	= $term;
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
									unset($terms[$index]);
								}
							}
						}
					}
				}
			}
			$activeCategory = null;
			if ($terms) {
				if (in_array('all videos',$terms)) {
					$activeCategory = -1;
				} else {
					$terms = $categoryMapper->findFromArray($terms);
					$categories = $terms;
					$activeCategory = array_pop($terms);
					$activeCategory = $activeCategory['id'];
					$subcategories = $categoryMapper->findChildrenIdAndName($activeCategory,$provinceId,$cityId);
				}
			}
			$mediaMapper 	= new \Townspot\Media\Mapper($this->getServiceLocator());
			$seriesMapper 	= new \Townspot\Series\Mapper($this->getServiceLocator());
			if ($activeCategory) {
				$data = array();
				if ($activeCategory < 0) {
					$matches = $mediaMapper->getDiscoverMedia($provinceId,$cityId,null,$sortTerm,$page);
				} else {
					$matches = $mediaMapper->getDiscoverMedia($provinceId,$cityId,$activeCategory,$sortTerm,$page);
				}
				foreach ($matches as $match) {    
					$media = $mediaMapper->find($match['id']);
					$video = array(
						'id'				=> $media->getId(),
						'type'				=> 'media',
						'link'				=> $media->getMediaLink(),
						'image'				=> $media->getResizerCdnLink(),
						'escaped_title'		=> $media->getTitle(false,true),
						'title'				=> $media->getTitle(),
						'logline'			=> $media->getLogline(),
						'escaped_logline'	=> $media->getLogline(true),
						'user'				=> $media->getUser()->getUsername(),
						'user_profile'		=> $media->getUser()->getProfileLink(),
						'duration'			=> $media->getDuration(true),
						'comment_count'		=> count($media->getCommentsAbout()),
						'views'				=> $media->getViews(),
						'location'			=> $media->getLocation(),
						'escaped_location'	=> $media->getLocation(false,true),
						'rate_up'			=> count($media->getRatings(true)),
						'rate_down'			=> count($media->getRatings(false)),
					);
					if ($match['series_id']) {
						$series = $seriesMapper->find($match['series_id']);
						$video['series_name']	= $series->getName();
						$video['series_link']	= $series->getSeriesLink();
					}
					$data[] = $video;
				}
			} else {
				$media = $mediaMapper->getRandom();
				$data[] = array(
					'id'				=> -1,
					'type'				=> 'category',
					'link'				=> $prefix . '/discover/all videos',
					'image'				=> $media->getResizerCdnLink(),
					'escaped_title'		=> 'All Videos',
					'title'				=> 'All Videos',
				);
				$categoryMapper 	= new \Townspot\Category\Mapper($this->getServiceLocator());
				$matches = $categoryMapper->getDiscoverCategories($provinceId,$cityId);
				foreach ($matches as $category) {  
					$media = $category->getRandomMedia();
					$data[] = array(
						'id'				=> $category->getId(),
						'type'				=> 'category',
						'link'				=> $category->getDiscoverLink(),
						'image'				=> $media->getResizerCdnLink(),
						'escaped_title'		=> $category->getName(),
						'title'				=> $category->getName(),
					);				
				}
			}
			$results = array(
				'searchId' 			=> $searchId,
				'matchesFound' 		=> count($data),
				'activeCategory'	=> $activeCategory,
				'provinceId'		=> $provinceId,
				'provinceName'		=> $provinceName,
				'cityId'			=> $cityId,
				'cityName'			=> $cityName,
				'prefix'			=> $prefix,
				'categories'		=> $categories,
				'subcategories' 	=> $subcategories,
				'data'				=> $data
			);
			$cache->setItem($searchId, $results);
		}
		if (count($results['data']) > 6) {
			$ad = array(
				array(
					'type'				=> 'ad',
					'title'				=> 'search',
				)
			);
			array_splice( $results['data'], 7, 0, $ad );
		}
		return $results;
	}
	
	/**
	 * Set Service Locator instance
	 *
	 * @param ServiceLocator $locator
	 * @return Object
	 */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->_serviceLocator = $serviceLocator;
        return $this;
    }

	/**
	 * Gets the value of isDebug
	 *
	 * @return boolean
	 */
	public function getServiceLocator()
	{
		return $this->_serviceLocator;
	}
}