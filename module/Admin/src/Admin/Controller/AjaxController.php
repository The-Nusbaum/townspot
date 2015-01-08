<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use \Townspot\Lucene\VideoIndex;

class AjaxController extends AbstractActionController
{
	public function __construct() 
	{
	}
	
    public function isAuthenticated()
    {
		if (!$this->zfcUserAuthentication()->hasIdentity()) {
			die;
		}
		if (!$this->isAllowed('admin')) {
			die;
		}
	}
	
    public function lookupAction()
    {
		$this->isAuthenticated();
		$response = array();
		$request = $this->getRequest();
        if ($request->isPost()) {
			$lookup = $request->getPost()->get('lookup');
			$ref_id = $request->getPost()->get('ref_id');
			switch ($lookup) {
				case 'cities':
					$response['cities'] = $this->_lookupCitiesHavingMediaAction($ref_id);

			}
		}
		$json = new JsonModel($response);
        return $json;
    }

    public function lookupeventAction()
    {
		$response = array();
		$this->isAuthenticated();
		$response = array();
		$request = $this->getRequest();
        if ($request->isPost()) {
			$objectMapper = new \Townspot\UserEvent\Mapper($this->getServiceLocator());
			$id = $request->getPost()->get('id');
			$userEvent = $objectMapper->find($id);
			$response = array(
				'title'			=> $userEvent->getTitle(),
				'url'			=> $userEvent->getUrl(),
				'started'		=> $userEvent->getStart()->format('m/d/Y h:i a'),
				'start_date'	=> $userEvent->getStart()->format('m/d/Y'),
				'start_time'	=> $userEvent->getStart()->format('h:i a'),
				'description'	=> $userEvent->getDescription(),
			);
		}
		$json = new JsonModel($response);
        return $json;
    }
	
    public function saveeventAction()
    {
		$response = array();
		$this->isAuthenticated();
		$response = array();
		$request = $this->getRequest();
        if ($request->isPost()) {
			$id = $request->getPost()->get('id');
			$objectMapper = new \Townspot\UserEvent\Mapper($this->getServiceLocator());
			$userEvent = $objectMapper->find($id);
			$userEvent->setTitle($request->getPost()->get('title'));
			$userEvent->setUrl($request->getPost()->get('url'));
			$userEvent->setDescription($request->getPost()->get('description'));
			$start = $request->getPost()->get('start_date') . ' ' . $request->getPost()->get('start_time');
			$start_time = strtotime($start);
			$datetime = new \DateTime();
			$datetime->setTimestamp($start_time);
			$userEvent->setStart($datetime);
			$objectMapper->setEntity($userEvent)->save();
		}
		$json = new JsonModel();
        return $json;
    }
	
    public function sendmessageAction()
    {
		$response = array();
		$this->isAuthenticated();
		$response = array();
		$request = $this->getRequest();
        if ($request->isPost()) {
            $key = uniqid();
			$id = $request->getPost()->get('user');
			$subject = $request->getPost()->get('subject');
			$body = $request->getPost()->get('body');
			$password = $request->getPost()->get('password');
			$objectMapper = new \Townspot\User\Mapper($this->getServiceLocator());
			$user = $objectMapper->find($id);
            $mail = new Mail\Message();
            $mail->setBody($body);
            $mail->setFrom('webmaster@townspot.tv', 'Townspot.tv');
            $mail->addTo($user->getEmail(), $user->getFirstName().' '.$user->getLastName());
            $mail->addBcc('emailcopy@townspot.tv');
            $mail->setSubject($subject);
            $transport = new Mail\Transport\Sendmail();
            $transport->send($mail);
			
			if ($password) {
                $sMessage  = "Please follow the link below to verify your account and reset your password\n\n";
                $sMessage .= "http://townspot.tv/verify/" . $key . "\n";
                $mail = new Mail\Message();
                $mail->setBody($sMessage);
                $mail->setFrom('webmaster@townspot.tv', 'Townspot.tv');
                $mail->addTo($user->getEmail(), $user->getFirstName().' '.$user->getLastName());
                $mail->addBcc('emailcopy@townspot.tv');
                $mail->setSubject('Forgot Password');
                $user->setSecurityKey($key);
                $userMapper->setEntity($user)->save();
			}
		}
		$json = new JsonModel();
        return $json;
	}

    public function userlistAction()
    {
		$results = array();
		$this->isAuthenticated();
		$request = $this->getRequest();
        if ($request->isPost()) {
			$options = array(
				'username'		=> $request->getPost()->get('username'),
				'province'		=> $request->getPost()->get('province'),
				'city'			=> $request->getPost()->get('city'),
				'after'			=> $request->getPost()->get('after'),
				'before'		=> $request->getPost()->get('before'),
				'status'		=> $request->getPost()->get('status'),
				'sort_field'	=> $request->getPost()->get('sort_field'),
				'sort_order'	=> $request->getPost()->get('sort_order'),
				'type'			=> $request->getPost()->get('type'),
			);
			$userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
			$cityMapper = new \Townspot\City\Mapper($this->getServiceLocator());
			$users = $userMapper->getAdminList($options);
			foreach ($users as $index => $user) {
				$users[$index]['joined'] = date('m/d/Y',strtotime($user['joined']));
				$users[$index]['location'] = ($users[$index]['location']) ?: 'Unknown';
			}
			$results = $users;
		}
		$json = new JsonModel($results);
        return $json;
    }

    public function medialistAction()
    {
		$results = array();
		$this->isAuthenticated();
		$request = $this->getRequest();
        if ($request->isPost()) {
			$options = array(
				'title'			=> $request->getPost()->get('title'),
				'username'		=> $request->getPost()->get('username'),
				'province'		=> $request->getPost()->get('province'),
				'city'			=> $request->getPost()->get('city'),
				'after'			=> $request->getPost()->get('after'),
				'before'		=> $request->getPost()->get('before'),
				'status'		=> $request->getPost()->get('status'),
				'sort_field'	=> $request->getPost()->get('sort_field'),
				'sort_order'	=> $request->getPost()->get('sort_order'),
				'type'			=> $request->getPost()->get('type'),
			);
			$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
			$cityMapper = new \Townspot\City\Mapper($this->getServiceLocator());
			$medias = $mediaMapper->getAdminList($options);
			
			foreach ($medias as $index => $media) {
				$medias[$index]['added'] = date('m/d/Y',strtotime($media['added']));
				$medias[$index]['location'] = ($medias[$index]['location']) ?: 'Unknown';
				$medias[$index]['series'] = ($medias[$index]['series']) ?: '----';
			}
			$results = $medias;
		}
		$json = new JsonModel($results);
        return $json;
    }

    public function serieslistAction()
    {
		$results = array();
		$this->isAuthenticated();
		$request = $this->getRequest();
        if ($request->isPost()) {
			$options = array(
				'title'			=> $request->getPost()->get('title'),
				'username'		=> $request->getPost()->get('username'),
				'sort_field'	=> $request->getPost()->get('sort_field'),
				'sort_order'	=> $request->getPost()->get('sort_order'),
				'type'			=> $request->getPost()->get('type'),
			);
			$seriesMapper = new \Townspot\Series\Mapper($this->getServiceLocator());
			$series = $seriesMapper->getAdminList($options);
			
			$results = $series;
		}
		$json = new JsonModel($results);
        return $json;
    }
	
    protected function _lookupCitiesHavingMediaAction($province_id)
    {
		$cityMapper = new \Townspot\City\Mapper($this->getServiceLocator());
		return $cityMapper->getCitiesHavingMedia($province_id);
    }
	
    public function deleterecordAction()
    {
		$request = $this->getRequest();
        if ($request->isPost()) {
			$type = $request->getPost()->get('type');
			$id = $request->getPost()->get('id');
			$ref = $request->getPost()->get('ref');
			$json = new JsonModel();
			switch ($type) {
				case 'Series':
					$objectMapper = new \Townspot\Series\Mapper($this->getServiceLocator());
					break;
				case 'User':
					$objectMapper = new \Townspot\User\Mapper($this->getServiceLocator());
					break;
				case 'Media':
					$objectMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
					break;
				case 'UserEvent':
					$objectMapper = new \Townspot\UserEvent\Mapper($this->getServiceLocator());
					break;
				case 'MediaComment':
					$objectMapper = new \Townspot\MediaComment\Mapper($this->getServiceLocator());
					break;
				case 'ArtistComment':
					$objectMapper = new \Townspot\ArtistComment\Mapper($this->getServiceLocator());
					break;
				case 'Follower':
					$objectMapper = new \Townspot\UserFollowing\Mapper($this->getServiceLocator());
					break;
				case 'Episode':
					$objectMapper = new \Townspot\SeriesEpisode\Mapper($this->getServiceLocator());
					break;
				case 'Favorite':
					$objectMapper = new \Townspot\User\Mapper($this->getServiceLocator());
					$objectMapper->deleteFavorite($id,$ref);
			        return $json;
					break;
			}
			$object = $objectMapper->find($id);
			$objectMapper->setEntity($object)->delete();
			return $json;
		}
	}
	
    public function mediasearchAction()
    {
		$results = array();
		$this->isAuthenticated();
		$request = $this->getRequest();
        if ($request->isPost()) {
			$options = array(
				'title'			=> $request->getPost()->get('title'),
				'username'		=> $request->getPost()->get('username'),
				'user_id'		=> $request->getPost()->get('user_id'),
				'category'		=> $request->getPost()->get('category'),
				'sort'			=> $request->getPost()->get('sort_field'),
				'sort_order'	=> $request->getPost()->get('sort_order')
			);
			$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
			$medias = $mediaMapper->getAvailableMedia($options);
			$results = $medias;
		}
		$json = new JsonModel($results);
        return $json;
	}
	
    public function updatesectionAction()
    {
		$results = array();
		$this->isAuthenticated();
		$request = $this->getRequest();
        if ($request->isPost()) {
			$section = $request->getPost()->get('section');
			$videos  = $request->getPost()->get('videos');
			$SectionMapper 		= new \Townspot\SectionBlock\Mapper($this->getServiceLocator());
			$_section = $SectionMapper->replaceMedia($section,$videos);
		}
		$json = new JsonModel();
        return $json;
	}
	
    public function mediaupdateAction()
    {
		$this->isAuthenticated();
		$videoId = $this->params()->fromRoute('id');
		$type = $this->params()->fromRoute('type');
		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		$userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
		if ($media = $mediaMapper->find($videoId)) {
			$user = $userMapper->find($this->zfcUserAuthentication()->getIdentity()->getId());
			if ($type == 'approve') {
				$media->setApproved(true);
                $encoding = new \Townspot\Encoding($this->getServiceLocator());
                $encoding->addToQueue($videoId);
			} else {
				$media->setApproved(false);
			}
			$media->setAdmin($user);
			$mediaMapper->setEntity($media)->save();
		}
		$json = new JsonModel();
        return $json;
	}

    public function encodingFinishedAction() {
        $encoding = new \Townspot\Encoding();
        die($encoding->finished());
    }
}
