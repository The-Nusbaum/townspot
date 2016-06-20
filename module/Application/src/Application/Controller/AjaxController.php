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
		$data = array();
        $searchId 		= $this->params()->fromPost('searchId');
        $page           = $this->params()->fromPost('page') ?: 1;
        $searchTerm 	= $this->params()->fromPost('searchTerm');
        $sortTerm 		= $this->params()->fromPost('sortTerm');
        list($sortField,$sortOrder) = explode(':',$sortTerm);
        $sortOrder  = ($sortOrder == 'desc') ? SORT_DESC : SORT_ASC;
		$search		= new \Townspot\Search\Search($this->getServiceLocator());
		$results    = $search->keywordSearch($searchTerm, $sortField, $sortOrder,$page);
        $json = new JsonModel($results);
        return $json;
    }

	public function similarVideosAction() {
		$id = $this->params()->fromRoute('id');
		$limit = $this->params()->fromRoute('limit');
		$page = $this->params()->fromRoute('page');

		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		$media = $mediaMapper->find($id);

		$results    = array();
		foreach($mediaMapper->getMediaLike($media,$limit) as $media) {
			$m = $media->toArray();
			$m['username'] = $media->getUser()->getDisplayName();
			$m['profileLink'] = $media->getUser()->getProfileLink();
			$m['mediaLink'] = $media->getMediaLink();
			$m['rate_up'] = count($media->getRatings(1));
			$m['rate_down'] = count($media->getRatings(0));
			$m['comment_count'] = count($media->getCommentsAbout());

			$results[] = $m;
		}
		$json = new JsonModel($results);
		return $json;
	}

    public function discoverresultsAction()
    {
		$data = array();
        $searchId 		= $this->params()->fromPost('searchId');
        $page           = $this->params()->fromPost('page') ?: 1;
        $terms 			= $this->params()->fromPost('terms');
        $sortTerm 		= $this->params()->fromPost('sortTerm');
		$search		= new \Townspot\Search\Search($this->getServiceLocator());
		$results    = $search->discoverSearch($terms, $sortTerm, $page);
        $json = new JsonModel($results);
        return $json;
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

	public function contactFansAction()
	{
		$userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
		$user = $userMapper->find($this->params()->fromRoute('id'));

		if ($user instanceof \Townspot\User\Entity) {
			$bodyPart = new \Zend\Mime\Message();
			$messageTemplate = "<p>%s, that you subscribed to on www.TownSpot.tv has sent you a message, displayed below.</p>

                            %s

                            <p>If you feel this message was inappropriate, please report it <a href='http://www.townspot.tv/contact-us'>here</a>.";

			$subjectTemplate = "%s on TownSpot has contacted you: %s";

			$subject = sprintf($subjectTemplate, $user->getDisplayName(), $this->params()->fromPost('subject'));

			$html = sprintf($messageTemplate, $user->getDisplayName(), $this->params()->fromPost('body')); //my html string here

			$bodyMessage = new \Zend\Mime\Part($html);
			$bodyMessage->type = 'text/html';

			$bodyPart->setParts(array($bodyMessage));

			$transport = new Mail\Transport\Sendmail();

			foreach ($user->getFollowedBy() as $f) {
				$m = new \Zend\Mail\Message();
				$m->addFrom('webmaster@townspot.tv', 'Townspot.tv')
						->addTo($f->getEmail(), $f->getDispayName())
						->setSubject($subject);

				$m->setBody($bodyPart);
				$m->setEncoding('UTF-8');

				$transport->send($m);
			}
		}
	}
}
