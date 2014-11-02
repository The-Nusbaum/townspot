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
use Zend\View\Model\JsonModel;
use \Townspot\Lucene\VideoIndex;

class AjaxController extends AbstractActionController
{
	public function __construct() 
	{
	}
	
	public function init() 
	{
	}

    public function explorelinkAction()
    {
		$response = array('FullName'	=> '',
						  'Link'		=> ''
		);
		$this->init();
		$request = $this->getRequest();
		$lat = 0;
		$long = 0;
        if ($request->isPost()) {
			if ($coords = $request->getPost()->get('coords')) {
				list($lat,$long) = explode(',',$coords);
			} else {
				$record = $this->getServiceLocator()->get('geoip')->getRecord();
				$lat = $record->getLatitude();
				$long = $record->getLongitude();
			}
			if (($lat)&&($long)) {
				$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
				$media = $mediaMapper->getClosest($lat,$long);
				$response = array('FullName'	=> $media->getCity()->getFullName(),
								  'Link'		=> $media->getCity()->getDiscoverLink()
				);
			}
		}
		$json = new JsonModel($response);
        return $json;
    }
	
    public function googleadsAction()
    {
		$response	= array('content');
		$config		= $this->getServiceLocator()->get('config');
		$config 	= $config['google_adsense'];
		$position	= 'leaderboard';
		$width		= 1268;
		
		$this->init();
		$request = $this->getRequest();
        if ($request->isPost()) {
			$position = $request->getPost()->get('position');
			$width = $request->getPost()->get('width');
		}
		if ($width <= 400) {
			$width = 'phone';
		} elseif ($width <= 500) {
			$width = 'mobile';
		} elseif ($width <= 800) {
			$width = 'tablet';
		} else {
			$width = 'desktop';
		}
		if (isset($config['ads'][$position][$width])) {
			$ad = $config['ads'][$position][$width];
		}
		if ($ad) {
			$content = '<div class="advertisement-title">advertisement</div>';
			if ($config['enable']) {
				$content .= sprintf(
					'<ins class="adsbygoogle" style="display:inline-block; width: %dpx; height: %dpx" 
					 data-ad-client="%s"
					 data-ad-slot="%s"></ins>',
					 $ad['width'],
					 $ad['height'],
					 $config['publisher-id'],
					 $ad['id']);
			} else {
				$content .= sprintf('<img src="http://placehold.it/%dx%d&text=%s-%s" />',
					$ad['width'],
					$ad['height'],
					$position,
					$width);
			}
		}
		$response = array(
			'content'	=> $content,
			'width'		=> $width,
		);
		$json = new JsonModel($response);
        return $json;
	}
	
    public function searchresultsAction()
    {
        $results = array(
			'reload'	=> false,
			'data'		=> array()
		);
		$data = array();
        $searchTerm		= $this->params()->fromPost('searchTerm');
        $sortTerm       = ($this->params()->fromPost('sortTerm')) ?: 'date:asc';
        $searchId 		= $this->params()->fromPost('searchId');
        $page           = $this->params()->fromPost('page') ?: 1;

		$cityMapper 	= new \Townspot\City\Mapper($this->getServiceLocator());
		$seriesMapper 	= new \Townspot\Series\Mapper($this->getServiceLocator());
		$userMapper 	= new \Townspot\User\Mapper($this->getServiceLocator());
		$mediaMapper 	= new \Townspot\Media\Mapper($this->getServiceLocator());

		if (!$searchId) {
			$searchId	= md5(serialize(array(session_id(),$searchTerm,$sortTerm)));
		}
        $cache			= $this->getServiceLocator()->get('cache-general');        
		$_results 		= $cache->getItem($searchId);
        if (!$_results) {
			$results['reload'] = true;
		} else {
			$startRange = ($page - 1) * 11;
			$pageResults = array_slice($_results,$startRange,11);
			foreach ($pageResults as $index => $result) {
				if ($result['type'] == 'city') {
					$city = $cityMapper->find($result['id']);
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
				} elseif ($result['type'] == 'series') {
					$series = $seriesMapper->find($result['id']);
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
				} elseif ($result['type'] == 'user') {
					$user = $userMapper->find($result['id']);
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
				} else {
					$media = $mediaMapper->find($result['id']);
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
			if (count($data) > 6) {
				$ad = array(
					array(
						'type'				=> 'ad',
						'title'				=> 'search',
					)
				);
				array_splice( $data, 7, 0, $ad );
			}
		}
		$results['data'] = $data;
        $json = new JsonModel($results);
        return $json;
    }
}
