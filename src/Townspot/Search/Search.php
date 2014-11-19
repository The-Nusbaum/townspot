<?php
namespace Townspot\Search;

use Townspot\Lucene\LocationIndex;
use Townspot\Lucene\ArtistIndex;
use Townspot\Lucene\SeriesIndex;
use Townspot\Lucene\VideoIndex;
use \ZendSearch\Lucene\Search\Query\MultiTerm;
use \ZendSearch\Lucene\Search\Query\Fuzzy;
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
			$_cities 	= array();
			$_artists 	= array();
			$_series 	= array();
			$_media 		= array();
			$cityData 	= array();
			$artistData	= array();
			$seriesData	= array();
			$mediaData	= array();
			
			$cityMapper 	= new \Townspot\City\Mapper($this->getServiceLocator());
			$seriesMapper 	= new \Townspot\Series\Mapper($this->getServiceLocator());
			$userMapper 	= new \Townspot\User\Mapper($this->getServiceLocator());
			$mediaMapper 	= new \Townspot\Media\Mapper($this->getServiceLocator());
			$locationIndex = new LocationIndex($this->getServiceLocator());
			$artistIndex   = new ArtistIndex($this->getServiceLocator());
			$mediaIndex    = new VideoIndex($this->getServiceLocator());
			$seriesIndex   = new SeriesIndex($this->getServiceLocator());
		
			\ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(
				new \ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive()
			);

			//City Search
			$query         = new Fuzzy(new Term($keyword, 'city'));
			$matches       = $locationIndex->find($query,'city',SORT_STRING,$sortOrder);
			foreach ($matches as $hit) {
				$_cities[] = $hit->objectid;
			}
			$query         = new Fuzzy(new Term($keyword, 'province'));
			$matches       = $locationIndex->find($query,'city',SORT_STRING,$sortOrder);
			foreach ($matches as $hit) {
				$_cities[] = $hit->objectid;
			}
			$_cities = array_unique($_cities);
			foreach ($_cities as $id) {
				$city = $cityMapper->find($id);
				$media = $city->getRandomMedia();
				$sortBy = strtotime($city->getFullName());
				$sortBy = strtolower($sortBy);	
				$sortBy = preg_replace('/[^a-z0-9 -]+/', '', $sortBy);		
				$cityData[$sortBy][] = array(
					'id'				=> $city->getId(),
					'type'				=> 'city',
					'link'				=> $city->getDiscoverLink(),
					'image'				=> $media->getResizerCdnLink(),
					'escaped_title'		=> $city->getFullName(),
					'title'				=> $city->getFullName(),
					'location'			=> $city->getFullName(),
					'escaped_location'	=> $city->getFullName(),
					'image_source'		=> $media->getSource(),
				);
			}

			//Artist Search
			$query         = new Fuzzy(new Term($keyword, 'username'));
			$matches       = $artistIndex->find($query,'username',SORT_STRING,$sortOrder);
			foreach ($matches as $hit) {
				$_artists[] = $hit->objectid;
			}
			$query         = new Fuzzy(new Term($keyword, 'artist_name'));
			$matches       = $artistIndex->find($query,'username',SORT_STRING,$sortOrder);
			foreach ($matches as $hit) {
				$_artists[] = $hit->objectid;
			}
			$_artists = array_unique($_artists);
			
			foreach ($_artists as $id) {
				$user = $userMapper->find($id);
				if ($media = $user->getRandomMedia()) {
					if ($sortField == 'created') {
						$sortBy = $user->getCreated()->getTimestamp();
					} else {
						$sortBy = $user->getUsername();
					}
					$sortBy = strtolower($sortBy);	
					$sortBy = preg_replace('/[^a-z0-9 -]+/', '', $sortBy);		
					$artistData[$sortBy][] = array(
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
						'image_source'		=> $media->getSource(),
					);
				}
			}
			//Series Search
			$query         = new Fuzzy(new Term($keyword, 'name'));
			$matches       = $seriesIndex->find($query,'name',SORT_STRING,$sortOrder);
			foreach ($matches as $hit) {
				$_series[] = $hit->objectid;
			}
			$query         = new Fuzzy(new Term($keyword, 'description'));
			$matches       = $seriesIndex->find($query,'name',SORT_STRING,$sortOrder);
			foreach ($matches as $hit) {
				$_series[] = $hit->objectid;
			}
/*
			$query         = new Fuzzy(new Term($keyword, 'media_titles'));
			$matches       = $seriesIndex->find($query,'name',SORT_STRING,$sortOrder);
			foreach ($matches as $hit) {
				$_series[] = $hit->objectid;
			}
			$query         = new Fuzzy(new Term($keyword, 'media_descriptions'));
			$matches       = $seriesIndex->find($query,'name',SORT_STRING,$sortOrder);
			foreach ($matches as $hit) {
				$_series[] = $hit->objectid;
			}
			$query         = new Fuzzy(new Term($keyword, 'media_loglines'));
			$matches       = $seriesIndex->find($query,'name',SORT_STRING,$sortOrder);
			foreach ($matches as $hit) {
				$_series[] = $hit->objectid;
			}
*/
			$_series = array_unique($_series);
			foreach ($_series as $id) {
				$series = $seriesMapper->find($id);
				$media = $series->getRandomMedia();
				if ($sortField == 'created') {
					$sortBy = $series->getCreated()->getTimestamp();
				} else {
					$sortBy = $series->getName();
				}
				$sortBy = strtolower($sortBy);	
				$sortBy = preg_replace('/[^a-z0-9 -]+/', '', $sortBy);		
				$seriesData[$sortBy][] = array(
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
					'image_source'		=> $media->getSource(),
				);
			}
			//Media Search
			$query         = new Fuzzy(new Term($keyword, 'title'));
			$matches       = $mediaIndex->find($query,'title',SORT_STRING,$sortOrder);
			foreach ($matches as $hit) {
				$_media[] = $hit->objectid;
			}
			$query         = new Fuzzy(new Term($keyword, 'logline'));
			$matches       = $mediaIndex->find($query,'logline',SORT_STRING,$sortOrder);
			foreach ($matches as $hit) {
				$_media[] = $hit->objectid;
			}
			$query         = new Fuzzy(new Term($keyword, 'description'));
			$matches       = $mediaIndex->find($query,'description',SORT_STRING,$sortOrder);
			foreach ($matches as $hit) {
				$_media[] = $hit->objectid;
			}
			$query         = new Fuzzy(new Term($keyword, 'series_name'));
			$matches       = $mediaIndex->find($query,'series_name',SORT_STRING,$sortOrder);
			foreach ($matches as $hit) {
				$_media[] = $hit->objectid;
			}
			$_media = array_unique($_media);
			foreach ($_media as $id) {
				$media = $mediaMapper->find($id);
				if ($sortField == 'created') {
					$sortBy = $media->getCreated()->getTimestamp();
				} elseif ($sortField == 'views') {
					$sortBy = $media->getViews();
				} else {
					$sortBy = $media->getTitle();
				}
				$sortBy = strtolower($sortBy);	
				$sortBy = preg_replace('/[^a-z0-9 -]+/', '', $sortBy);	
				$added = false;
				if ($episodes = $media->getEpisode()) {
					if ($episode = $episodes[0]) {
						if ($series = $episode->getSeries()) {
							$mediaData[$sortBy][] = array(
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
								'image_source'		=> $media->getSource(),
							);
							$added = true;
						}
					}
				}
				if (!$added) {	
					$mediaData[$sortBy][] = array(
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
						'image_source'		=> $media->getSource(),
					);
				}
			}
			if ($sortOrder == SORT_DESC) {
				krsort($cityData);
				krsort($artistData);
				krsort($seriesData);
				krsort($mediaData);
			} else {
				ksort($cityData);
				ksort($artistData);
				ksort($seriesData);
				ksort($mediaData);
			}
			$data 		= array();
			foreach ($cityData as $key => $cities) {
				$data = array_merge($data,$cities);
			}
			foreach ($artistData as $key => $artists) {
				$data = array_merge($data,$artists);
			}
			foreach ($seriesData as $key => $series) {
				$data = array_merge($data,$series);
			}
			foreach ($mediaData as $key => $media) {
				$data = array_merge($data,$media);
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
		$results = false;
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
						'image_source'		=> $media->getSource(),
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
					'image_source'		=> $media->getSource(),
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
						'image_source'		=> $media->getSource(),
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