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
use Facebook\Helpers\FacebookRedirectLoginHelper;
use Facebook\FacebookSDKException;

class VideoController extends AbstractActionController
{

	protected $_api_info = array(
	  'applicationId' => 'Townspot',
	  'clientId' => "872367745273-sbsiuc81kh9o70ok3macc15d2ebpl440.apps.googleusercontent.com",
	  'developerId' => "AIzaSyCa1RYJsf-C94cTQo34GC59DkiijUq_54s",
	  'clientSecret' => 'KkR-tZy_lJmcHHzlKtFWDAdD',
	  'scope' => 'https://www.googleapis.com/auth/youtube.readonly'
	);

	protected $_vimeo_info = array(
		'id' => '938463ff9c7bc67df401a52dbac998c04153ce99',
		'secret' => 'VDgEGqc6tmL9rmaksJMJT+PwvkrAE8+LQLsatL92Z3Lc4ZM9T0taXZZN+SOeuH2zdYG14skXtu/rVPhtU6erAXVIInyUMPQtNqZvX6ztmgpfLkt4tTdNRfzHbLTWf416',
		'authUrl' => 'https://api.vimeo.com/oauth/authorize'

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
			$up = 0;
			$down = 0;
			$ratings = $ratingMapper->findByMedia($media);
			foreach($ratings as $r){
				if($r->getRating()) $up++;
				else $down++;
			}
			$response = array(
				'rate_up'	=> $up,
				'rate_down'	=> $down,
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
		if (!$this->isAllowed('user')) {
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

		$vimeoForm = new \Application\Forms\Video\Upload\VimeoForm();
		$this->_view->setVariable('vimeoForm',$vimeoForm);

		$dmForm = new \Application\Forms\Video\Upload\DailymotionForm();
		$this->_view->setVariable('dmForm',$dmForm);

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
		$commentCount = count($comments);
		$comments = array_slice($comments,$startRange,$pagelimit);
		$comments = array(
			'count' => $commentCount,
			'comments' => $comments
		);

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

		$countryMapper = new \Townspot\Country\Mapper($this->getServiceLocator());
		$country = $countryMapper->find($data->get('country_id'));

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
		$this->_view->setVariable('country',$country->getName());
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

				if (preg_match('/youtube\.com/', $url)) {
					preg_match('/v=([^&]*)/i', $data->get('youtube_url'), $idMatches);
					if (!empty($idMatches[1])) {
						$id = $idMatches[1];
					} else {
						//no id
					}
				} elseif (preg_match('/youtu.be/', $url)) {
					$id = preg_replace('/https?:\/\/youtu.be\/(.*)/', '$1', $url);
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
				preg_match('/([0-9]*)H/', $duration, $hours);
				preg_match('/([0-9]*)M/', $duration, $minutes);
				preg_match('/([0-9]*)S/', $duration, $seconds);

				$durationInSecs = 0;
				if (!empty($hours[1])) $durationInSecs += $hours[1] * 60 * 60;
				if (!empty($minutes[1])) $durationInSecs += $minutes[1] * 60;
				if (!empty($seconds[1])) $durationInSecs += $seconds[1];

				$duration = $durationInSecs;

				$data->set('title', $ytVideo->getSnippet()->getTitle())
					->set('description', $ytVideo->getSnippet()->getDescription())
					->set('duration', $duration)
					->set('on_media_server', 1)
					->set('preview_url', $ytVideo->getSnippet()->getThumbnails()->getHigh()->getUrl())
					->set('previewImage', $ytVideo->getSnippet()->getThumbnails()->getHigh()->getUrl())
					->set('source', 'youtube')
					->set('video_url', $data->get('youtube_url'))
					->set('url', $data->get('youtube_url'));
			} elseif($data->get('vimeo_url') && !$data->get('review_ok')) {
				$url = $data->get('vimeo_url');
				$id = str_replace('https*://vimeo.com/','',$data->get('vimeo_url'));
				$vimeo = new \Vimeo\Vimeo('ac278d2d73248632ac83bf9fc43900876b9c12e0', '68c3d6ee56c6a66a2c4e6f05c06f0199f84b94c3');
				$token = '45dd4e70cfd1a1b307c683c1b5deff2a';
				$vimeo->setToken($token);
				$response = $vimeo->request("/videos/$id");

				$data->set('title', $response['body']['name'])
					->set('description', $response['body']['description'])
					->set('duration', $response['body']['duration'])
					->set('on_media_server', 1)
					->set('preview_url', $response['body']['pictures']['sizes'][5]['link'])
					->set('previewImage', $response['body']['pictures']['sizes'][3]['link'])
					->set('source', 'vimeo')
					->set('video_url', $url)
					->set('views', $response['body']['stats']['plays'])
					->set('url', $url);
			} elseif($data->get('dm_url') && !$data->get('review_ok')) {
				$url = $data->get('dm_url');
				//http://www.dailymotion.com/video/x36ujyx_guys-get-90s-boyband-makeovers_fun
				preg_match('/\/(x.*?)_/',$data->get('dm_url'),$matches);
				$id = $matches[1];

				$api = new \Dailymotion();
				$response = $api->get(
					"/video/$id",
					array('fields' => array('id', 'title', 'owner', 'allow_embed', 'embed_url', 'poster_url', 'thumbnail_url',
						'views_total', 'description', 'duration'))
				);

				$data->set('title', $response['title'])
					->set('description', $response['description'])
					->set('duration', $response['duration'])
					->set('on_media_server', 1)
					->set('preview_url', $response['thumbnail_url'])
					->set('previewImage', $response['thumbnail_url'])
					->set('source', 'dailymotion')
					->set('video_url', $url)
					->set('views', $response['views_total'])
					->set('url', $url);
			} elseif(!$data->get('review_ok')) {
				//do nothing
			} else {
				if($data->get('source') == 'youtube') {
					$url = $data->get('youtube_url');
				} else {
					$url = $data->get('video_url');
				}

				$mediaEntity = new \Townspot\Media\Entity();
				$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
				$mediaEntity->setUser($user)
					->setCountry($country)
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

				if($data->get('on_media_server')) {
					$mediaEntity->setOnMediaServer($data->get('on_media_server'));
				}

				$categoryMapper = new \Townspot\Category\Mapper($this->getServiceLocator());
				if (is_array($data->get('selCat'))) foreach($data->get('selCat') as $vc) {
					$cat = $categoryMapper->find($vc);
					$mediaEntity->addCategory($cat);
				}

				$mediaMapper->setEntity($mediaEntity);
				$mediaMapper->save();
				$trackingMapper = new \Townspot\Tracking\Mapper($this->getServiceLocator());
						$tracking = new \Townspot\Tracking\Entity();
						$tracking->setUser($user->getId())
										 ->setType("{$data->get('source')}_upload")
								 ->setValue($mediaEntity->getId());
				$trackingMapper->setEntity($tracking)->save();
								$_SESSION['flash'] = array();
								$_SESSION['flash'][] = 'Your video upload was successful. It will be reviewed by our staff for content and quality.';
				//$this->flashMessenger()->addMessage('Your video upload was successful. It will be reviewed by our staff for content and quality.');
				return $this->redirect()->toRoute('upload');
			}
		}


		return $this->_view;
	}

	public function seriesAction() {
		$id = $this->params()->fromRoute('id');
		$seriesMapper = new \Townspot\Series\Mapper($this->getServiceLocator());
		$series = $seriesMapper->find($id);

		$mediaList = $series->getEpisodes();
		$index = array();
		foreach($mediaList as $episode) {
			$media = $episode->getMedia();
			$categories = array();
			$cats = $media->getCategories();
			foreach($cats as $c) {
				$categories[] = array(
					'name' => $c->getName(),
					'url' => $c->getDiscoverLink(null, false)
				);
			}
			$video = array(
				'id' => $media->getId(),
				'type' => 'media',
				'link' => $media->getMediaLink(),
				'image' => $media->getResizerCdnLink(),
				'escaped_title' => $media->getTitle(false, true),
				'title' => $media->getTitle(),
				'logline' => $media->getLogline(),
				'escaped_logline' => $media->getLogline(true),
				'user' => $media->getUser()->getUsername(),
				'user_profile' => $media->getUser()->getProfileLink(),
				'duration' => $media->getDuration(true),
				'comment_count' => count($media->getCommentsAbout()),
				'views' => $media->getViews(false),
				'location' => $media->getLocation(),
				'escaped_location' => $media->getLocation(false, true),
				'rate_up' => count($media->getRatings(true)),
				'rate_down' => count($media->getRatings(false)),
				'image_source' => $media->getSource(),
				'created' => $media->getCreated()->getTimestamp(),
				'url' => $media->getUrl(),
				'city' => $media->getCity()->getName(),
				'state' => $media->getProvince()->getName(),
				'categories' => $categories,
				'description' => $media->getDescription()
			);

			$index[$media->getId()] = $video;
		}
		$this->getServiceLocator()
			->get('ViewHelperManager')
			->get('HeadScript')
			->appendFile('/js/videointeractions.js','text/javascript');
		return new ViewModel(compact('seasonsList','mediaList','index','series'));
	}

	public function reportAction() {
		$id = $this->params()->fromRoute('id');
		$emailAddy = $this->params()->fromPost('email');
		$reason = $this->params()->fromPost('reason');
		$details = $this->params()->fromPost('details');

		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		$media = $mediaMapper->find($id);

	$body = <<<EOT
	Video flagged for: - $reason
	Contact: - $emailAddy
	Details: $details

	Video: {$media->getId()} - {$media->getTitle()}
EOT;

		$email = new Message();
		$email->addFrom($emailAddy);
		$email->addTo("josh@townspot.tv");
		$email->setSubject("$emailAddy - $reason - {$media->getTitle()}");
		$email->setBody($body);

		if (APPLICATION_ENV == 'production') {
			$transport = new \Zend\Mail\Transport\Sendmail();
			$transport->send($email);
		} else {
			print_r($email);
		}
		die;

	}

	public function hireArtistAction(){
		$id = $this->params()->fromRoute('id');
		$post = $this->params()->fromPost();

		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		$media = $mediaMapper->find($id);

		$user = $media->getUser();

		$email = new Message();
		$email->addFrom($post['email']);

		$subject = "Townspot: ";

		if(!empty($post['bizname'])) $subject .= $post['bizname'];
		elseif(!empty($post['venueName'])) $subject .= $post['venueName'];
		else $subject .= $post['name'];

		$subject .= ' would like to hire you';

		$email->setSubject($subject);

		$body = "<p>Hello {$user->getDisplayName()},</p>

		<p>A representative for a potential business opportunity has contacted you from your profile on TownSpot.tv. Please see their message below to determine if this is an opportunity you'd like to pursue.</p>

		";

		$body .= "<dl>";

		foreach($post as $field => $val) {
			switch($field) {
				case 'name':
					$fieldName = "Contact Name:";
					break;
				case 'email':
					$fieldName = "Contact Email:";
					break;
				case 'phone':
					$fieldName = "Contact Phone:";
					break;
				case 'locations':
					$fieldName = "Locations Served:";
					break;
				case 'experience':
					$fieldName = "Previous Experience:";
					break;
				case 'venueName':
					$fieldName = "Venue Name:";
					break;
				case 'bizname':
					$fieldName = "Business Name:";
					break;
				case 'jobTitle':
					$fieldName = "Job Title:";
					break;
				case 'desc':
					$fieldName = "Description:";
					break;
				case 'dates':
					$fieldName = "Dates in Question:";
					break;
				case 'website':
					$fieldName = "Website:";
					$val = "<a href='$val' target='_new'>$val</a>";
					break;
				case 'budget':
					$fieldName = "Budget (optional):";
					break;
				case 'services':
					$fieldName = "Services Offered:";
					break;
				case 'message':
					$fieldName = "Message:";
					break;
			}
			$body .= "<dt>$fieldName</dt><dd>$val</dd>";
		}

		$body .= "</dl>";

		$body .= "<p>If you feel this opportunity is inappropriate, spam, or offensive in any way, please let us know by emailing admin@townspot.tv so we can take appropriate action.</p>

	<p>Thanks!</p>";

		$email->addTo($user->getEmail());

		$body = new \Zend\Mime\Part($body);
		$body->type = 'text/html';

		$message = new \Zend\Mime\Message();
		$message->setParts(compact('body'));

		$email->setBody($message);

		if (APPLICATION_ENV == 'production') {
			$transport = new \Zend\Mail\Transport\Sendmail();
			$transport->send($email);
		} else {
			print_r($email);
		}
		die;
	}

	private function _fb() {
		return new \Facebook\Facebook([
		  'app_id' => '333808790029898',
		  'app_secret' => '7315cc6812046cf91419959bdd359bec',
		  'default_graph_version' => 'v2.5',
		]);
	}

	private function _ytDuration($v) {
		$duration = $v->getContentDetails()['duration'];

		preg_match('/(\d*)H/',$duration,$h);
		preg_match('/(\d*)M/',$duration,$m);
		preg_match('/(\d*)S/',$duration,$s);

		if(count($h)) $h = $h[1] * 60 * 60;
		if(count($m)) $m = $m[1] * 60;
		if(count($s)) $s = $s[1];

		return intval($h) + intval($m) + intval($s);
	}

	public function ytVideosAction() {
		$this->_view->setTemplate('application/video/pick-videos');

		$code = $this->params()->fromQuery('code');

		$client = new \Google_Client();
		$client->setApplicationName($this->_api_info['applicationId']);
		$client->setDeveloperKey($this->_api_info['developerId']);
		$client->setClientId($this->_api_info['clientId']);
		$client->setClientSecret($this->_api_info['clientSecret']);
		$client->setRedirectUri("http://{$_SERVER['HTTP_HOST']}/videos/youtube-videos");

		if(!$code) {
			$auth = new \Google_Auth_OAuth2($client);
			$url = $auth->createAuthUrl(\Google_Service_YouTube::YOUTUBE_READONLY);
			return $this->redirect()->toUrl($url);
		}
		$client->authenticate($this->params()->fromQuery('code'));

		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());


		$yt = new \Google_Service_YouTube($client);

		$ytChannel = $yt->channels->listChannels("contentDetails",array('mine' => "true"))->getItems()[0];
		$plId = $ytChannel->getContentDetails()->getRelatedPlaylists()->getUploads();
		$ytPlaylist = $yt->playlistItems->listPlaylistItems("contentDetails",array('playlistId' => $plId));
		$ids = array();
		foreach($ytPlaylist->getItems() as $item) {
			$ids[] = $item->getContentDetails()['videoId'];
		}

		$items = array();
		$in_system = array();
		$videos = array();
		foreach($ids as $i => $id) {
			$v = $yt->videos->listVideos("snippet,contentDetails,statistics",array('id' => $id))->getItems()[0];

			$duration = $this->_ytDuration($v);

			$video = array(
					'id'			=> $v->getId(),
					'url' 			=> "https://www.youtube.com/watch?v=$id",
					'thumbnail' 	=> $v->getSnippet()['thumbnails']['high']['url'],
					'title'			=> $v->getSnippet()['title'],
					'duration'		=> $duration,
					'description'	=> $v->getSnippet()['description'],
			);

			$media = $mediaMapper->findOneByUrl("https://www.youtube.com/watch?v=$id");
			if($media) $video['in_system'] = true;;
			$videos[] = $video;
		}
		$this->_view->setVariable('videos',$videos);
		$this->_view->setVariable('source','youtube');
		return $this->_view;
	}

	protected function _twitch($code) {
		$twitch = new \stdClass();
		$twitch->secret = 'ijygmuxd0w02fuvq185rj9s9nmkkcg';
		$twitch->id = '3nbquwcvdzqzrp99qqs6zpiiqx7ppe';
		$twitch->code = $code;
		$twitch->state = (empty($_SESSION['twitchState']))? $_SESSION['twitchState'] = uniqid() : $_SESSION['twitchState'];
		$twitch->redirect = "http://townspot.tv/videos/twitch-videos";
		$twitch->scope = "channel_feed_read";


		$apiCalls = new \stdClass();

		$apiCalls->authorizeUrl = sprintf("https://api.twitch.tv/kraken/oauth2/authorize?response_type=code&client_id=%s&redirect_uri=%s&scope=%s&state=%s",
			$twitch->id,
			$twitch->redirect,
			$twitch->scope,
			$twitch->state
		);

		$apiCalls->getToken = function($twitch) {
			$ch = curl_init("https://api.twitch.tv/kraken/oauth2/token");

			$body = array(
				"client_id" 	=> $twitch->id,
				"client_secret" => $twitch->secret,
				"grant_type" 	=> "authorization_code",
				"redirect_uri" 	=> $twitch->redirect,
				"code" 			=> $twitch->code,
				"state" 		=> $twitch->state
			);

			$body = http_build_query($body);

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt($ch, CURLOPT_POST,           1 );
			curl_setopt($ch, CURLOPT_POSTFIELDS,     $body );
			curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain'));

			$result = curl_exec($ch);

			return json_decode($result);
		};

		$twitch->api = $apiCalls;
		return $twitch;
	}

	public function twitchVideosAction() {
		ini_set("display_startup_errors", 1); ini_set("display_errors", 1); /* Reports for either E_ERROR. E_WARNING. E_NOTICE. Any Error*/ error_reporting(E_ALL);
		$this->_view->setTemplate('application/video/pick-videos');
		//get code
		$code = $this->params()->fromQuery('code');
		//get state
		$state = $this->params()->fromQuery('state');
		//get twitch data
		$twitch = $this->_twitch($code);
		//have code? get vids
		if($code) {
		//no? get code asshole!
			$response = $twitch->getToken($twitch);

			var_dump($response);die;
		} else {
			header("Location: $twitch->authorizeUrl");
			die;
		}
		//spit out vids
	}

	public function fbVideosAction() {
		$this->_view->setTemplate('application/video/pick-videos');
		$code = $this->params()->fromQuery('code');
		$state = $this->params()->fromQuery('state');
		$fb = $this->_fb();

		$helper = $fb->getRedirectLoginHelper();

		if(!$code) {
			$permissions = ['user_videos'];
			$loginUrl = $helper->getLoginUrl("http://{$_SERVER['HTTP_HOST']}/videos/fb-videos", $permissions);
			header("Location: $loginUrl");
			die;
		} else {
			try {
			  $accessToken = $helper->getAccessToken();
			  $accessToken = $fb->getOAuth2Client()->getLongLivedAccessToken($accessToken);
			  $_SESSION['fb-token'] = $accessToken;
				$fb->setDefaultAccessToken($accessToken);
			} catch(Facebook\Exceptions\FacebookResponseException $e) {
			  // When Graph returns an error
			  echo 'Graph returned an error: ' . $e->getMessage();
			  exit;
			} catch(Facebook\Exceptions\FacebookSDKException $e) {
			  // When validation fails or other local issues
			  echo 'Facebook SDK returned an error: ' . $e->getMessage();
			  exit;
			}
			$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
			$fbvideos = $fb->get('/me/videos/uploaded?fields=title,description,source,length,picture')->getDecodedBody()["data"];
			$videos = array();
			foreach($fbvideos as $k =>$v) {
				$m = $mediaMapper->findOneByUrl($v['source']);
				$video = array(
						'id'			=> $v['id'],
						'url' 			=> $v['source'],
						'thumbnail' 	=> $v['picture'],
						'title'			=> $v['title'],
						'duration'		=> $v['length'],
						'description'	=> $v['description'],
				);
				if($m instanceof \Townspot\Media\Entity) $video['in_system'] = true;
				$videos[] = $video;
			}
			$this->_view->setVariable('videos',$videos);
			$this->_view->setVariable('source','facebook');
			return $this->_view;
		}
	}

	public function vimeoVideosAction() {
		$this->_view->setTemplate('application/video/pick-videos');
		$creds = $this->_vimeo_info;
		$vimeo = new \Vimeo\Vimeo($creds['id'],$creds['secret']);
		$code = $this->params()->fromQuery('code');

		if(!$code) {
			$state = time();
			$_SESSION['state'] = $state;
			return $this->redirect()->toUrl(
					$vimeo->buildAuthorizationEndpoint(
							"http://{$_SERVER['HTTP_HOST']}/videos/vimeo-videos",
							array('public'),
							$state
					)
			);
		}

		$token = $vimeo->accessToken($code, "http://{$_SERVER['HTTP_HOST']}/videos/vimeo-videos");
		$vimeo->setToken($token['body']['access_token']);
		$_SESSION['vimeoToken'] = $token['body']['access_token'];
		$raw = $vimeo->request("/me/videos");

		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		foreach($raw['body']['data'] as $v) {
			$media = $mediaMapper->findByUrl($v['link']);
			$video = array(
					'id' 			=> $v['uri'],
					'url' 			=> $v['uri'],
					'thumbnail' 	=> $v['pictures']['sizes'][2]['link'],
					'title'			=> $v['name'],
					'duration'		=> $v['duration'],
					'description'	=> $v['description'],
			);
			if($media) $video['in_system'] = true;

			$videos[] = $video;
		}
		$this->_view->setVariable('videos', $videos);
		$this->_view->setVariable('source','vimeo');
		return $this->_view;
	}

	private function _dm() {
		$apiKey = '6c5729a40a0ee0ac7bcc';
		$apiSecret = 'f901c80d60fe2abdecc4c4a08e24ea35c931a166';

		$api = new \Dailymotion();
		$api->setGrantType(\Dailymotion::GRANT_TYPE_AUTHORIZATION, $apiKey, $apiSecret);
		return $api;
	}

	public function dailymotionVideosAction() {
		$this->_view->setTemplate('application/video/pick-videos');
		$api = $this->_dm();

		try
		{
			// The following line will actually try to authenticate before making the API call.
			// * The SDK takes care of retrying if the access token has expired.
			// * The SDK takes care of storing the access token itself using its `readSession()`
			//   and `storeSession()` methods that are made to be overridden in an extension
			//   of the class if you want a different storage than provided by default.
			$result = $api->get(
					'/me/videos',
					array('fields' => array('id', 'title', 'owner', 'allow_embed',
							'embed_url', 'poster_url', 'thumbnail_url',
							'views_total', 'description', 'duration', 'url')
					)
			);
		}
		catch (\DailymotionAuthRequiredException $e)
		{
			// If the SDK doesn't have any access token stored in memory, it tries to
			// redirect the user to the Dailymotion authorization page for authentication.
			return $this->redirect()->toUrl($api->getAuthorizationUrl());
		}
		catch (\DailymotionAuthRefusedException $e)
		{

			// Handle the situation when the user refused to authorize and came back here.
			// <YOUR CODE>
		}

		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());

		$videos = array();

		foreach($result['list'] as $v){
			$media = $mediaMapper->findByUrl($v['url']);
			$video = array(
					'id' 			=> $v['id'],
					'url' 			=> $v['url'],
					'thumbnail' 	=> $v['thumbnail_url'],
					'title'			=> $v['title'],
					'duration'		=> $v['duration'],
					'description'	=> $v['description'],
			);
			if($media) $video['in_system'] = true;

			$videos[] = $video;
		}
		$this->_view->setVariable('videos',$videos);
		$this->_view->setVariable('source','dailymotion');
		return $this->_view;
	}

	public function reviewYtAction()
	{
		$this->_view->setTemplate('application/video/review-ext');
		$client = new \Google_Client();
		$client->setApplicationName($this->_api_info['applicationId']);
		$client->setDeveloperKey($this->_api_info['developerId']);
		$client->setClientId($this->_api_info['clientId']);
		$client->setClientSecret($this->_api_info['clientSecret']);

		$userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
		$user = $userMapper->find($this->auth->getIdentity());
		$this->_view->setVariable('user', $user);

		$yt = new \Google_Service_YouTube($client);

		$ids = $this->params()->fromPost('data');

		$videos = array();
		foreach ($ids as $id) {
			$v = $yt->videos->listVideos("snippet,contentDetails,statistics",array('id' => $id))->getItems()[0];
			$duration = $this->_ytDuration($v);
			$video = array(
					'id'			=> $v->getId(),
					'url' 			=> "https://www.youtube.com/watch?v=$id",
					'thumbnail' 	=> $v->getSnippet()['thumbnails']['high']['url'],
					'title'			=> $v->getSnippet()['title'],
					'duration'		=> $duration,
					'description'	=> $v->getSnippet()['description'],
			);
			$videos[] = $video;
		}

		$this->_view->setVariable('videos', $videos);
		$this->_view->setVariable('source', 'youtube');
		return $this->_view;
	}

	public function reviewFbAction()
	{
		$this->_view->setTemplate('application/video/review-ext');
		$fb = $this->_fb();
		$fb->setDefaultAccessToken($_SESSION['fb-token']);

		$userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
		$user = $userMapper->find($this->auth->getIdentity());
		$this->_view->setVariable('user',$user);

		$ids = $this->params()->fromPost('data');
		foreach ($ids as $id) {
			$v = $fb->get("/$id?fields=title,description,source,length,picture")->getDecodedBody();
			$video = array(
					'id'			=> $v['id'],
					'url' 			=> $v['source'],
					'thumbnail' 	=> $v['picture'],
					'title'			=> $v['title'],
					'duration'		=> ceil($v['length']),
					'description'	=> $v['description'],
			);
			$videos[] = $video;
		}
		$this->_view->setVariable('videos', $videos);
		$this->_view->setVariable('source', 'facebook');
		return $this->_view;
	}

	public function reviewVimeoAction()
	{
		$this->_view->setTemplate('application/video/review-ext');
		$creds = $this->_vimeo_info;
		$vimeo = new \Vimeo\Vimeo($creds['id'],$creds['secret']);
		$vimeo->setToken($_SESSION['vimeoToken']);

		$userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
		$user = $userMapper->find($this->auth->getIdentity());
		$this->_view->setVariable('user', $user);

		$ids = $this->params()->fromPost('data');

		foreach ($ids as $id) {
			$v = $vimeo->request(urldecode($id))['body'];
			$video = array(
					'id' 			=> $v['uri'],
					'url' 			=> $v['uri'],
					'thumbnail' 	=> $v['pictures']['sizes'][2]['link'],
					'title'			=> $v['name'],
					'duration'		=> $v['duration'],
					'description'	=> $v['description'],
			);
			$videos[] = $video;
		}
		//var_dump('<pre>',$videos);die;
		$this->_view->setVariable('videos', $videos);
		$this->_view->setVariable('source', 'vimeo');
		return $this->_view;
	}

	public function reviewDailymotionAction()
	{
		$this->_view->setTemplate('application/video/review-ext');
		$api = $this->_dm();

		$userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
		$user = $userMapper->find($this->auth->getIdentity());
		$this->_view->setVariable('user', $user);

		$ids = $this->params()->fromPost('data');

		foreach ($ids as $id) {
			$v = $api->get("/video/$id",
				array('fields' => array('id', 'title', 'owner', 'allow_embed',
					'embed_url', 'poster_url', 'thumbnail_url',
					'views_total', 'description', 'duration', 'url'
				)
			));

			$video = array(
					'id' 			=> $v['id'],
					'url' 			=> $v['url'],
					'thumbnail' 	=> $v['thumbnail_url'],
					'title'			=> $v['title'],
					'duration'		=> $v['duration'],
					'description'	=> $v['description'],
			);
			$videos[] = $video;
		}
		//var_dump('<pre>',$videos);die;
		$this->_view->setVariable('videos', $videos);
		$this->_view->setVariable('source', 'dailymotion');
		return $this->_view;
	}

	public function submitAction() {
		$userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		$trackingMapper = new \Townspot\Tracking\Mapper($this->getServiceLocator());
		$countryMapper = new \Townspot\Country\Mapper($this->getServiceLocator());
		$provinceMapper = new \Townspot\Province\Mapper($this->getServiceLocator());
		$cityMapper = new \Townspot\City\Mapper($this->getServiceLocator());

		$user = $userMapper->find($this->zfcUserAuthentication()->getIdentity()->getId());

		$data = $this->params()->fromPost('data');

		$count = 0;

		foreach ($data as $id => $v) {
			$city = $cityMapper->find($v['city_id']);
			$province = $provinceMapper->find($v['province_id']);
			$country = $countryMapper->find($v['country_id']);
			$video = new \Townspot\Media\Entity();
			$video->setUser($user)
					->setCountry($country)
					->setProvince($province)
					->setCity($city)
					->setTitle($v['title'])
					->setDescription($v['description'])
					->setUrl($v['url'])
					->setPreviewImage($v['thumbnail'])
					->setAuthorised($v['authorised'])
					->setAllowContact($v['contact'])
					->setSource($v['source'])
					->setDuration($v['duration'])
					->setOnMediaServer(true);
			$mediaMapper->setEntity($video)->save();

			$tracking = new \Townspot\Tracking\Entity();
			$tracking->setUser($user->getId())
					->setType("{$v['source']}_upload")
					->setValue($video->getId());
			$trackingMapper->setEntity($tracking)->save();

			$count++;
		}
		$_SESSION['flash'] = array();
		if ($count > 1) $_SESSION['flash'][] = "Your upload of $count videos was successful. They will be reviewed by our staff for content and quality.";
		else $_SESSION['flash'][] = 'Your video upload was successful. It will be reviewed by our staff for content and quality.';

		return $this->redirect()->toRoute('upload');
	}

}
