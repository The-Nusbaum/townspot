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
			$cityData		= array();
			$artistData		= array();
			$seriesData		= array();
			$mediaData		= array();
			$keywords = preg_split('/[\s,]/',$keyword);
			$matches = array();
			
			foreach ($keywords as $word) {
				$wordMatches = $this->wordSearch($word);
				foreach ($wordMatches as $type => $matchCount) {
					foreach ($matchCount as $id => $scores) {
						$score = array_sum($scores) / count($scores);
						if (isset($matches[$type][$id])) {
							$matches[$type][$id][] = $score;
						} else {
							$matches[$type][$id] = array($score);
						}
					}
				}
			}

			foreach ($matches as $type => $matchCount) {
				foreach ($matchCount as $id => $scores) {
					if (count($scores) == count($keywords)) {
						$score = array_sum($scores) / count($scores);
						$matches[$type][$id] = $score;
					} else {
						unset($matches[$type][$id]);
					}
				}
			}
			
			$cityMapper 	= new \Townspot\City\Mapper($this->getServiceLocator());
			$seriesMapper 	= new \Townspot\Series\Mapper($this->getServiceLocator());
			$userMapper 	= new \Townspot\User\Mapper($this->getServiceLocator());
			$mediaMapper 	= new \Townspot\Media\Mapper($this->getServiceLocator());
		
			if (isset($matches['cities'])) {
				foreach ($matches['cities'] as $id => $score) {
					$city = $cityMapper->find($id);
					$media = $city->getRandomMedia();
					if ($sortField == 'relevance') {
						$sortBy = $score;
					} else {
						$sortBy = $city->getFullName();
					}
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
						'score'				=> $score,
					);
				}
			}
			if (isset($matches['artists'])) {
				foreach ($matches['artists'] as $id => $score) {
					$user = $userMapper->find($id);
					if ($media = $user->getRandomMedia()) {
						if ($sortField == 'relevance') {
							$sortBy = $score;
						} elseif ($sortField == 'created') {
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
							'score'				=> $score,
						);
					}
				}
			}
			if (isset($matches['series'])) {
				foreach ($matches['series'] as $id => $score) {
					$series = $seriesMapper->find($id);
					$media = $series->getRandomMedia();
					if ($sortField == 'relevance') {
						$sortBy = $score;
					} elseif ($sortField == 'created') {
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
						'score'				=> $score,
					);
				}
			}

			if (isset($matches['media'])) {
				foreach ($matches['media'] as $id => $score) {
					$media = $mediaMapper->find($id);
					if ($sortField == 'relevance') {
						$sortBy = $score;
					} elseif ($sortField == 'created') {
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
									'score'				=> $score,
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
			}
			// Temporary Disable Series Matches
			$seriesData = array();
			
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
			//$cache->setItem($searchId, $results);
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
        $searchId       = md5(serialize(array($terms)));
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
					$matches = $mediaMapper->getDiscoverMedia($provinceId,$cityId,null);
				} else {
					$matches = $mediaMapper->getDiscoverMedia($provinceId,$cityId,$activeCategory);
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
						'views'				=> $media->getViews(false),
						'location'			=> $media->getLocation(),
						'escaped_location'	=> $media->getLocation(false,true),
						'rate_up'			=> count($media->getRatings(true)),
						'rate_down'			=> count($media->getRatings(false)),
						'image_source'		=> $media->getSource(),
						'created'			=> $media->getCreated()->getTimestamp(),
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
					'created'			=> $media->getCreated()->getTimestamp(),
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
		if (!$sortTerm) {
			$sortTerm = 'created:desc';
		}
        list($sortField,$sortOrder) = explode(':',$sortTerm);
		
		$_sortedData = array();
		foreach ($results['data'] as $media) {
			if ($media['type'] == 'media') {
				$matchstring = strtolower(trim($media[$sortField]));
				$matchstring = preg_replace("/^[^0-9a-z]+/","",$matchstring);
				$_sortedData[$matchstring][$media['id']] = $media;
			}
		}
		if ($_sortedData) {
			if ($sortOrder == 'desc') {
				krsort($_sortedData);
			} else {
				ksort($_sortedData);
			}
			$sortedData = array();
			foreach ($_sortedData as $key => $media) {
				$sortedData = array_merge($sortedData,$media);
			}
			$results['data'] = $sortedData;
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
	
    protected function wordSearch($keyword,$threshold = 0.8)
    {
		$results 	= array();

		$locationIndex = new LocationIndex($this->getServiceLocator());
		$artistIndex   = new ArtistIndex($this->getServiceLocator());
		$mediaIndex    = new VideoIndex($this->getServiceLocator());
		$seriesIndex   = new SeriesIndex($this->getServiceLocator());
		
		\ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(
			new \ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive()
		);

		//City Search
		$query         = new Fuzzy(new Term($keyword, 'city'),$threshold);
		$matches       = $locationIndex->find($query,'city');
		foreach ($matches as $hit) {
			if (isset($results['cities'][$hit->objectid])) {
				$results['cities'][$hit->objectid][] = $hit->score;
			} else {
				$results['cities'][$hit->objectid] = array($hit->score);
			}
		}
		
		$query         = new Fuzzy(new Term($keyword, 'province'),$threshold);
		$matches       = $locationIndex->find($query,'province');
		foreach ($matches as $hit) {
			if (isset($results['cities'][$hit->objectid])) {
				$results['cities'][$hit->objectid][] = $hit->score;
			} else {
				$results['cities'][$hit->objectid] = array($hit->score);
			}
		}

		//Artist Search
		$query         = new Fuzzy(new Term($keyword, 'username'),$threshold);
		$matches       = $artistIndex->find($query,'username');
		foreach ($matches as $hit) {
			if (isset($results['artists'][$hit->objectid])) {
				$results['artists'][$hit->objectid][] = $hit->score;
			} else {
				$results['artists'][$hit->objectid] = array($hit->score);
			}
		}
		$query         = new Fuzzy(new Term($keyword, 'artist_name'),$threshold);
		$matches       = $artistIndex->find($query,'artist_name');
		foreach ($matches as $hit) {
			if (isset($results['artists'][$hit->objectid])) {
				$results['artists'][$hit->objectid][] = $hit->score;
			} else {
				$results['artists'][$hit->objectid] = array($hit->score);
			}
		}
			
		//Series Search
		$query         = new Fuzzy(new Term($keyword, 'name'),$threshold);
		$matches       = $seriesIndex->find($query,'name');
		foreach ($matches as $hit) {
			if (isset($results['series'][$hit->objectid])) {
				$results['series'][$hit->objectid][] = $hit->score;
			} else {
				$results['series'][$hit->objectid] = array($hit->score);
			}
		}
		$query         = new Fuzzy(new Term($keyword, 'description'),$threshold);
		$matches       = $seriesIndex->find($query,'description');
		foreach ($matches as $hit) {
			if (isset($results['series'][$hit->objectid])) {
				$results['series'][$hit->objectid][] = $hit->score;
			} else {
				$results['series'][$hit->objectid] = array($hit->score);
			}
		}
		
		$query         = new Fuzzy(new Term($keyword, 'media_titles'),$threshold);
		$matches       = $seriesIndex->find($query,'media_titles');
		foreach ($matches as $hit) {
			if (isset($results['series'][$hit->objectid])) {
				$results['series'][$hit->objectid][] = $hit->score;
			} else {
				$results['series'][$hit->objectid] = array($hit->score);
			}
		}

		$query         = new Fuzzy(new Term($keyword, 'media_descriptions'),$threshold);
		$matches       = $seriesIndex->find($query,'media_descriptions');
		foreach ($matches as $hit) {
			if (isset($results['series'][$hit->objectid])) {
				$results['series'][$hit->objectid][] = $hit->score;
			} else {
				$results['series'][$hit->objectid] = array($hit->score);
			}
		}

		$query         = new Fuzzy(new Term($keyword, 'title'),$threshold);
		$matches       = $mediaIndex->find($query,'title');
		foreach ($matches as $hit) {
			if (isset($results['media'][$hit->objectid])) {
				$results['media'][$hit->objectid][] = $hit->score;
			} else {
				$results['media'][$hit->objectid] = array($hit->score);
			}
		}
		
		$query         = new Fuzzy(new Term($keyword, 'logline'),$threshold);
		$matches       = $mediaIndex->find($query,'logline');
		foreach ($matches as $hit) {
			if (isset($results['media'][$hit->objectid])) {
				$results['media'][$hit->objectid][] = $hit->score;
			} else {
				$results['media'][$hit->objectid] = array($hit->score);
			}
		}
		
		$query         = new Fuzzy(new Term($keyword, 'description'),$threshold);
		$matches       = $mediaIndex->find($query,'description');
		foreach ($matches as $hit) {
			if (isset($results['media'][$hit->objectid])) {
				$results['media'][$hit->objectid][] = $hit->score;
			} else {
				$results['media'][$hit->objectid] = array($hit->score);
			}
		}
		$query         = new Fuzzy(new Term($keyword, 'series_name'),$threshold);
		$matches       = $mediaIndex->find($query,'series_name');
		foreach ($matches as $hit) {
			if (isset($results['media'][$hit->objectid])) {
				$results['media'][$hit->objectid][] = $hit->score;
			} else {
				$results['media'][$hit->objectid] = array($hit->score);
			}
		}
		return $results;
	}
}