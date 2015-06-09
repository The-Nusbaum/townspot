<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

require_once APPLICATION_PATH . "/google/apiclient/src/Google/autoload.php";

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Mail\Message;
use Zend\Mvc\Controller\Plugin\FlashMessenger;

class VideoController extends AbstractActionController
{

    protected $_api_info = array(
        'applicationId' => 'Townspot',
        'clientId' => "872367745273-sbsiuc81kh9o70ok3macc15d2ebpl440.apps.googleusercontent.com",
        'developerId' => "AIzaSyCa1RYJsf-C94cTQo34GC59DkiijUq_54s",
    );

	public function __construct() 
	{
        $this->_view = new ViewModel();
        $this->auth = new \Zend\Authentication\AuthenticationService();
        $this->_view->setVariable('authdUser',$this->auth->getIdentity());

        if ($this->flashMessenger()->hasMessages()) {
            $this->_view->setVariable('flash',$this->flashMessenger()->getMessages());
        }

	}
	
	public function init($title = null) 
	{
		$title = $title ?: "TownSpot &bull; Your Town. Your Talent. Spotlighted";
		$this->getServiceLocator()
			 ->get('ViewHelperManager')
			 ->get('HeadTitle')
			 ->set($title . ' - townspot.tv');
	}
	
    public function playerAction()
    {
		$videoId = $this->params()->fromRoute('id');
		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		if ($media = $mediaMapper->find($videoId)) {
			$url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$facebookInfo = array(
				'title'			=> $media->getTitle(),
				'description'	=> str_replace('"',"'",strip_tags($media->getDescription())),
				'url'			=> $url,
                'image'			=> $media->getResizerCdnLink(700,535),
                'width'			=> 700
			);
			$twitterInfo = array(
				'title'			=> $media->getTitle(),
				'description'	=> str_replace('"',"'",strip_tags($media->getDescription())),
				'url'			=> $url,
                'image'			=> $media->getResizerCdnLink(435,326),
                'width'			=> 435
			);

			$this->getServiceLocator()
				->get('ViewHelperManager')
				->get('HeadScript')
				->appendFile('/js/videointeractions.js','text/javascript');
			$this->getServiceLocator()
				->get('ViewHelperManager')
				->get('HeadMeta')
				->appendName('description', strip_tags($media->getDescription()));
			$this->init($media->getTitle());
			$relatedMedia  = $mediaMapper->getMediaLike($media);
			$this->layout()->facebookInfo = $facebookInfo;
			$this->layout()->twitterInfo = $twitterInfo;
			$results = array(
				'media'    		=> $media,
				'related'  		=> $relatedMedia,
			);
		} else {
			$this->getResponse()->setStatusCode(404);
			return; 
		}
		return new ViewModel( $results );
    }
	
    public function embedAction()
    {
		$this->layout('application/embed');
		$videoId = $this->params()->fromRoute('id');
		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		if ($media = $mediaMapper->find($videoId)) {
			$this->init($media->getTitle());
			$viewModel = new ViewModel();
			$viewModel->setTerminal(true);
			$viewModel->setVariables(
				array(
					'media_id' => $videoId,
					'media'    => $media,
				)
			);
			return $viewModel;
		} else {
			//Error
		}
		return null;
	}

    public function ratingsAction()
    {
		$response = array(
			'rate_up'	=> 0,
			'rate_down'	=> 0,
			'my_rating'	=> '',
		);
		$videoId = $this->params()->fromRoute('id');
		$_rating = $this->params()->fromPost('rating');
		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		$ratingMapper = new \Townspot\Rating\Mapper($this->getServiceLocator());
		$userRating = null;
		if ($media = $mediaMapper->find($videoId)) {
			if ($this->zfcUserAuthentication()->hasIdentity()) {
				$userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
				$loggedInUser = $userMapper->find($this->zfcUserAuthentication()->getIdentity()->getId());
				foreach ($media->getRatings() as $rating) {
					if ($rating->getUser() == $loggedInUser) {
						$userRating = $rating;
					}
				}
				switch ($_rating) {
					case 'up':
						if (!$userRating) {
							$userRating = new \Townspot\Rating\Entity();
							$userRating->setUser($loggedInUser)
									   ->setMedia($media);
						}
						$userRating->setRating(true);
						$ratingMapper->setEntity($userRating)->save();
					break;
					case 'down':
						if (!$userRating) {
							$userRating = new \Townspot\Rating\Entity();
							$userRating->setUser($loggedInUser)
									   ->setMedia($media);
						}
						$userRating->setRating(false);
						$ratingMapper->setEntity($userRating)->save();
					break;
				}
			}
			$response = array(
				'rate_up'	=> count($media->getRatings(true)),
				'rate_down'	=> count($media->getRatings(false)),
				'my_rating'	=> '',
			);
			if ($userRating) {
				$response['my_rating'] = ($userRating->getRating()) ? 'up' : 'down';
			}
		}
		$json = new JsonModel($response);
        return $json;
	}
	
    public function favoriteAction()
    {
		$response = array(
			'favorite'	=> false,
		);
		$videoId = $this->params()->fromRoute('id');
		$toggle = $this->params()->fromPost('toggle');
		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		$userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
		if ($media = $mediaMapper->find($videoId)) {
			$isFavorite = false;
			if ($this->zfcUserAuthentication()->hasIdentity()) {
				$loggedInUser = $userMapper->find($this->zfcUserAuthentication()->getIdentity()->getId());
				$hasFavorite = false;
				foreach ($loggedInUser->getFavorites() as $index => $favorite) {
					if ($favorite->getId() == $videoId) {
						$hasFavorite = true;
						if ($toggle) {
							$loggedInUser->removeFavorite($index);
							$userMapper->setEntity($loggedInUser)->save();
						} else {
							$response = array(
								'favorite'	=> true,
							);
						}
					}
				}
				if ((!$hasFavorite)&&($toggle)) {
					$loggedInUser->addFavorite($media);
					$userMapper->setEntity($loggedInUser)->save();
					$response = array(
						'favorite'	=> true,
					);
				}
			}
		}
		$json = new JsonModel($response);
        return $json;
	}
	
    public function followartistAction() 
	{
		$response = array(
			'following'	=> false,
		);
		$videoId = $this->params()->fromRoute('id');
		$follow = $this->params()->fromPost('follow');
		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		$userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
		$userFollowingMapper = new \Townspot\UserFollowing\Mapper($this->getServiceLocator());
		if ($media = $mediaMapper->find($videoId)) {
			$isFollowing = false;
			if ($this->zfcUserAuthentication()->hasIdentity()) {
				$loggedInUser = $userMapper->find($this->zfcUserAuthentication()->getIdentity()->getId());
				foreach ($loggedInUser->getFollowing() as $index => $following) {
					if ($following->getFollower()->getId() == $media->getUser()->getId()) {
						$isFollowing = true;
						if ($follow) {
							$loggedInUser->removeFollowing($index);
							$userMapper->setEntity($loggedInUser)->save();
						} else {
							$response = array(
								'following'	=> true,
							);
						}
					}
				}
				if ((!$isFollowing)&&($follow)) {
					$userFollowing = new \Townspot\UserFollowing\Entity();
					$userFollowing->setUser($loggedInUser)
								  ->setFollower($media->getUser())
								  ->setShareEmail(($follow == 'true'));
					$userFollowingMapper->setEntity($userFollowing)->save();
					$response = array(
						'following'	=> true,
					);
				}
			}
		}
		$json = new JsonModel($response);
        return $json;
	}
	
    public function contactartistAction() 
	{
		$videoId = $this->params()->fromRoute('id');

		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		$userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
		$userFollowingMapper = new \Townspot\UserFollowing\Mapper($this->getServiceLocator());
		if ($media = $mediaMapper->find($videoId)) {
			if ($this->zfcUserAuthentication()->hasIdentity()) {
				$loggedInUser = $userMapper->find($this->zfcUserAuthentication()->getIdentity()->getId());
				$email = new Message();
				$email->addFrom($loggedInUser->getEmail(),$loggedInUser->getUserName());
				$email->addTo($media->getUser()->getEmail());
				$email->setSubject($this->params()->fromPost('subject'));
				$email->setBody($this->params()->fromPost('message'));

				if (APPLICATION_ENV == 'production') {
					$transport = new Mail\Transport\Sendmail();
					$transport->send($email);				
				} else {
					print_r($email);
				}
			}
		}
		die;
	}
	
    public function uploadAction()
    {
        if (!$this->isAllowed('artist')) {
            return $this->redirect()->toRoute('zfcuser-login');
        }
        $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
        $user = $userMapper->findOneById($this->auth->getIdentity());

        $countryMapper = new \Townspot\Country\Mapper($this->getServiceLocator());
        $countries = $countryMapper->findAll();

        $provinceMapper = new \Townspot\Province\Mapper($this->getServiceLocator());
        $provinces = $provinceMapper->findByCountry($user->getCountry());

        $cityMapper = new \Townspot\City\Mapper($this->getServiceLocator());
        $cities = $cityMapper->findByProvince($user->getProvince());

        $categoryMapper = new \Townspot\Category\Mapper($this->getServiceLocator());
        $categories = $categoryMapper->findByParent(0);

        $headForm = new \Application\Forms\Video\Upload\HeadForm('uploadHeader', $countries, $provinces, $cities);

        $data = array(
            'user_id' => $user->getId(),
            'country_id' => $user->getCountry()->getId(),
            'province_id' => $user->getProvince()->getId(),
            'city_id' => $user->getCity()->getId()
        );

        $headForm->setData($data);
        $this->_view->setVariable('headForm',$headForm);

        $ytForm = new \Application\Forms\Video\Upload\YtForm();
        $this->_view->setVariable('ytForm',$ytForm);

        $manualForm = new \Application\Forms\Video\Upload\ManualForm('manualForm');
        $this->_view->setVariable('manualForm',$manualForm);

        $footerForm = new \Application\Forms\Video\Upload\FooterForm('footerForm');
        $this->_view->setVariable('footerForm',$footerForm);

        $videoMediaForm = new \Application\Forms\Video\Upload\VideoMediaForm('videoMediaForm');
        $this->_view->setVariable('videoMediaForm',$videoMediaForm);

        $this->getServiceLocator()
            ->get('viewhelpermanager')
            ->get('HeadScript')->appendFile('/js/upload.js');

		return $this->_view;
	}
	
    public function deleteAction()
    {
		print "Delete";
		die;
	}
	
    public function relatedAction()
    {
		$videoId = $this->params()->fromRoute('id');
		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		if ($media = $mediaMapper->find($videoId)) {
			$related  = $mediaMapper->getMediaLike($media);
			header("Content-type: text/xml; charset=utf-8");
			$output  = "<rss version=\"2.0\" xmlns:media=\"http://search.yahoo.com/mrss/\">\n";
			$output.= '<channel>';
			foreach ($related as $relatedMedia) {
				$output .= '    <item>';
				$output .= '	    <title>' . $relatedMedia->getTitle(false) . '</title>';
				$output .= '	    <link>http://' . $this->getRequest()->getServer('HTTP_HOST') . $relatedMedia->getMediaLink() . '</link>';
				if ($relatedMedia->getSource() == 'internal') {
					$output .= '	    <media:thumbnail url="http://images.townspot.tv/resizer.php?id=' . $relatedMedia->getId() . '" />';
				} else {
					$output .= '	    <media:thumbnail  url="' . $relatedMedia->getPreviewImage() . '" />';
				}
				$output .= '	    <media:content url="' . $relatedMedia->getMediaUrl('SD') . '"  type="video/mp4"/>';
				$output .= '    </item>';
			}
            $output .= '</channel>';
			$output .= "</rss>";
            print $output;
		} else {
			//Error
		}
		die;			
	}
	
    public function commentsAction()
    {
		$videoId = $this->params()->fromRoute('id');
		$pagelimit = ($this->params()->fromPost('pagelimit')) ?: 5;
		$page = ($this->params()->fromPost('page')) ?: 1;
		$comment = ($this->params()->fromPost('comment')) ?: null;
		$delete = ($this->params()->fromPost('delete')) ?: null;
		$loggedInUser = null;
		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		$userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
		$mediaCommentMapper = new \Townspot\MediaComment\Mapper($this->getServiceLocator());
		if ($this->zfcUserAuthentication()->hasIdentity()) {
			$loggedInUser = $userMapper->find($this->zfcUserAuthentication()->getIdentity()->getId());
		}
		
		if ($comment) {
			if ($media = $mediaMapper->find($videoId)) {
				$mediaComment = new \Townspot\MediaComment\Entity();
				$mediaComment->setUser($loggedInUser)
							 ->setTarget($media)
							 ->setComment($comment);
				$mediaCommentMapper->setEntity($mediaComment)->save();
			}
			die;
		}
		if ($delete) {
			$comment = $mediaCommentMapper->find($delete);
			$mediaCommentMapper->setEntity($comment)->delete();
			die;
		}
		
		$_comments = $mediaCommentMapper->findByMediaId($videoId);
		$comments = array();
		foreach ($_comments as $comment) {
			$comments[] = array(
				'id' 			=> $comment->getId(),
				'candelete'		=> ($loggedInUser) ? ($comment->getUser()->getId() == $loggedInUser->getId()) : false,
				'username' 		=> $comment->getUser()->getUsername(),
				'profileLink'	=> $comment->getUser()->getProfileLink(),
				'profileImage' 	=> 'http://images.townspot.tv/' . $comment->getUser()->getProfileImage(),
				'comment' 		=> $comment->getComment(),
				'created' 		=> $comment->getCreated()->format('c'),
			);
		}
		$startRange = ($page - 1) * $pagelimit;
		$comments = array_slice($comments,$startRange,$pagelimit);
		$json = new JsonModel($comments);
        return $json;
	}
	
    public function successAction()
    {
		print "Success";
		die;
	}
	
    public function flagAction()
    {
		die;
	}
	
    public function addcommentAction()
    {
		print "Add Comment";
		die;
	}
	
    public function removecommentAction()
    {
		print "Remove Comment";
		die;
	}

    protected function _tierCats($cats) {
        $categoryMapper = new \Townspot\Category\Mapper($this->getServiceLocator());
        $data = array();
        foreach($cats as $c) {
            if($c instanceof \Townspot\Category\Entity) {
                $categories = $c->getCategoryTree();
                foreach($categories as $k => $t) {
                    switch($k) {
                        case 0:
                            if(empty($data[$t[0]])) {
                                $data[$t[0]] = array(
                                    'id' => $t[0],
                                    'name' => $t[1],
                                    'children' => array()
                                );
                            }
                            break;
                        case 1;
                            if(empty($data[$categories[0][0]]['children'][$t[0]])) {
                                $data[$categories[0][0]]['children'][$t[0]] = array(
                                    'id' => $t[0],
                                    'name' => $t[1],
                                    'children' => array()
                                );
                            }
                            break;
                        case 2;
                            if(empty($data[$categories[0][0]]['children'][$categories[1][0]][$t[0]])) {
                                $data[$categories[0][0]]['children'][$categories[1][0]][$t[0]] = array(
                                    'id' => $t[0],
                                    'name' => $t[1],
                                    'children' => false
                                );
                            }
                            break;
                    }

                }
            }
        }
        return $data;
    }

    public function editAction() {
        $id = $this->params()->fromRoute('id');
        $mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
        $media = $mediaMapper->find($id);

        //this tells the IDE what class we have as well as checks for a non-empty object
        if($media instanceof \Townspot\Media\Entity) {
            if($media->getUser()->getId() == $this->auth->getIdentity()) {
                if(!$this->getRequest()->isPost()) {

                    $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
                    $user = $userMapper->findOneById($this->auth->getIdentity());

                    $countryMapper = new \Townspot\Country\Mapper($this->getServiceLocator());
                    $countries = $countryMapper->findAll();

                    $provinceMapper = new \Townspot\Province\Mapper($this->getServiceLocator());
                    $provinces = $provinceMapper->findByCountry($user->getCountry());

                    $cityMapper = new \Townspot\City\Mapper($this->getServiceLocator());
                    $cities = $cityMapper->findByProvince($user->getProvince());

                    $categoryMapper = new \Townspot\Category\Mapper($this->getServiceLocator());
                    $categories = $categoryMapper->findByParent(0);

                    $headForm = new \Application\Forms\Video\Upload\HeadForm('uploadHeader', $countries, $provinces, $cities);

                    $data = $media->toArray();
                    $data['selectedCategories'] = $this->_tierCats($media->getCategories());
                    if ($data['source'] == 'youtube') $data['youtube_url'] = $data['url'];

                    $headForm->setData($data);
                    $this->_view->setVariable('headForm', $headForm);

                    $mainForm = new \Application\Forms\Video\EditForm('manualForm');
                    $mainForm->setData($data);
                    $this->_view->setVariable('mainForm', $mainForm);

                    $videoMediaForm = new \Application\Forms\Video\Upload\VideoMediaForm('videoMediaForm');
                    $videoMediaForm->setData($data);
                    $this->_view->setVariable('videoMediaForm', $videoMediaForm);

                    $this->_view->setVariable('data', $data);

                    $this->getServiceLocator()
                        ->get('viewhelpermanager')
                        ->get('HeadScript')->appendFile('/js/edit.js');
                } else {
                    foreach($this->params()->fromPost() as $field => $value) {
                        if(method_exists($media,"set".ucfirst($field))) {
                            $method = "set".ucfirst($field);
                            $media->$method($value);
                        }
                    }
                    foreach($media->getCategories() as $key => $cat) {
                        $media->removeCategory($key);
                    }
                    $mediaMapper->setEntity($media)->save();

                    $categoryMapper = new \Townspot\Category\Mapper($this->getServiceLocator());
                    foreach($this->params()->fromPost('selCat') as $cat_id) {
                        $cat = $categoryMapper->find($cat_id);
                        $media->addCategory($cat);
                    }

                    $mediaMapper->setEntity($media)->save();

                    $this->flashMessenger()->addMessage('Your video has been edited.');

                    return $this->redirect()->toRoute('dashboard');

                }
            } else {
                //throw exception and 403
            }
        } else {
            //throw exception and 404
        }

        return $this->_view;
    }

    public function reviewAction() {
        $request = $this->getRequest();
        $data = $request->getPost();
        $this->_view->setVariable('data',$data);

        $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
        $user = $userMapper->find($data->get('user_id'));
/*
        $countryMapper = new \Townspot\Country\Mapper($this->getServiceLocator());
        $country = $countryMapper->find($data->get('country_id'));
*/
        $provinceMapper = new \Townspot\Province\Mapper($this->getServiceLocator());
        $province = $provinceMapper->find($data->get('province_id'));

        $cityMapper = new \Townspot\City\Mapper($this->getServiceLocator());
        $city = $cityMapper->find($data->get('city_id'));

        $categoryMapper = new \Townspot\Category\Mapper($this->getServiceLocator());
        $categories = $categoryMapper->findAll();
        foreach($categories as $c) {
            $allCategories[$c->getId()] = $c->getName();
        }

        $this->_view->setVariable('state',$province->getName());
        $this->_view->setVariable('city',$city->getName());
        $this->_view->setVariable('allCategories',$allCategories);

        if($this->getRequest()->isPost()){
            if($data->get('youtube_url') && !$data->get('review_ok')) {
                $url = $data->get('youtube_url');
                /*
                $client = new \Zend\Http\Client('https://example.org', array(
                    'adapter' => 'Zend\Http\Client\Adapter\Curl'
                ));
                $client->getAdapter()->setCurlOption(CURLOPT_SSL_VERIFYPEER, false);
                $yt = new \ZendGData\YouTube(
                    $client,
                    'Townspot',
                    "872367745273-sbsiuc81kh9o70ok3macc15d2ebpl440.apps.googleusercontent.com",
                    "AI39si7sp4rb57_29xMFWO2AoT8DDc0dKYklw7_IsUYpEhsxSL-DNK60f3eIF7OK_Iy0_xYm1eAxX2skGO57B6oHd6qJHHiPZA"

                );
                */

                if(preg_match('/youtube\.com/',$url)) {
                    preg_match('/v=([^&]*)/i', $data->get('youtube_url'), $idMatches);
                    if (!empty($idMatches[1])) {
                        $id = $idMatches[1];
                    } else {
                        //no id
                    }
                } elseif(preg_match('/youtu.be/',$url)) {
                    $id = preg_replace('/https?:\/\/youtu.be\/(.*)/','$1',$url);
                } else {
                    $_SESSION['flash'] = array();
                    $_SESSION['flash'][] = 'Invalid url provided';
                    return $this->redirect()->toRoute('upload');
                }

                $client = new \Google_Client();
                $client->setApplicationName($this->_api_info['applicationId']);
                $client->setDeveloperKey($this->_api_info['developerId']);
                $yt = new \Google_Service_YouTube($client);
                $ytVideo = $yt->videos->listVideos("snippet,contentDetails,statistics",
                    array('id' => $id))->getItems()[0];
                //$ytVideo = $yt->getVideoEntry($id);

                $duration = $ytVideo->getContentDetails()->getDuration();
                preg_match('/([0-9]*)H/',$duration,$hours);
                preg_match('/([0-9]*)M/',$duration,$minutes);
                preg_match('/([0-9]*)S/',$duration,$seconds);

                $durationInSecs = 0;
                if(!empty($hours[1])) $durationInSecs += $hours[1] * 60 * 60;
                if(!empty($minutes[1])) $durationInSecs += $minutes[1] * 60;
                if(!empty($seconds[1])) $durationInSecs += $seconds[1];

                $duration = $durationInSecs;

                $data->set('title',$ytVideo->getSnippet()->getTitle())
                    ->set('description',$ytVideo->getSnippet()->getDescription())
                    ->set('duration',$duration)
                    ->set('on_media_server',1)
                    ->set('preview_url',$ytVideo->getSnippet()->getThumbnails()->getHigh()->getUrl())
                    ->set('previewImage',$ytVideo->getSnippet()->getThumbnails()->getHigh()->getUrl())
                    ->set('source','youtube')
                    ->set('video_url',$data->get('youtube_url'))
                    ->set('url',$data->get('youtube_url'));

            } elseif(!$data->get('review_ok')) {
                //do nothing?
            } else {
                if($data->get('source') == 'youtube') {
                    $url = $data->get('youtube_url');
                } else {
                    $url = $data->get('video_url');
                }

                $mediaEntity = new \Townspot\Media\Entity();
                $mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
                $mediaEntity->setUser($user)
                    //->setCountry($country)
                    ->setProvince($province)
                    ->setCity($city)
                    ->setTitle($data->get('title'))
                    ->setLogline($data->get('logline'))
                    ->setDescription($data->get('description'))
                    ->setUrl($url)
                    ->setPreviewImage($data->get('preview_url'))
                    ->setAuthorised($data->get('authorised'))
                    ->setAllowContact($data->get('allow_contact'))
                    ->setSource($data->get('source'))
                    ->setDuration($data->get('duration'));

                $categoryMapper = new \Townspot\Category\Mapper($this->getServiceLocator());
                if (is_array($data->get('selCat'))) foreach($data->get('selCat') as $vc) {
                    $cat = $categoryMapper->find($vc);
                    $mediaEntity->addCategory($cat);
                }

                $mediaMapper->setEntity($mediaEntity);
                $mediaMapper->save();
		$_SESSION['flash'] = array();
		$_SESSION['flash'][] = 'Your video upload was successful. It will be reviewed by our staff for content and quality.';
                //$this->flashMessenger()->addMessage('Your video upload was successful. It will be reviewed by our staff for content and quality.');
                return $this->redirect()->toRoute('upload');
            }
        }


        return $this->_view;
    }
	
	
	
}
