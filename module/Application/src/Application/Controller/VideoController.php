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
use Zend\Mail\Message;

class VideoController extends AbstractActionController
{
	public function __construct() 
	{
        $this->_view = new ViewModel();
        $this->auth = new \Zend\Authentication\AuthenticationService();
        $this->_view->setVariable('authdUser',$this->auth->getIdentity());
	}
	
	public function init($title = null) 
	{
		$title = $title ?: "TownSpot &bull; Your Town. Your Talent. Spotlighted";
		$this->getServiceLocator()
			 ->get('ViewHelperManager')
			 ->get('HeadTitle')
			 ->set($title);
			 ->set('TownSpot &bull; Your Town. Your Talent. Spotlighted');

	}
	
    public function playerAction()
    {
		$videoId = $this->params()->fromRoute('id');
		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		if ($media = $mediaMapper->find($videoId)) {
			$this->getServiceLocator()
				 ->get('ViewHelperManager')
				 ->get('HeadScript')
				 ->appendFile('/js/videointeractions.js','text/javascript');
			$this->init($media->getTitle());
			$relatedMedia  = $mediaMapper->getMediaLike($media);
			return new ViewModel(
				array(
					'media'    => $media,
					'related'  => $relatedMedia
				)
			);
		} else {
			//Error
		}
		return null;
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
				$loggedInUser = $this->zfcUserAuthentication()->getIdentity()->getId();
				foreach ($media->getRatings() as $rating) {
					if ($rating->getUser()->getId() == $loggedInUser) {
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
	
	
	
}
