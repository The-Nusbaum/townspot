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
			
			$results = $medias;
		}
		$json = new JsonModel($series);
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
			}
			$object = $objectMapper->find($id);
			$objectMapper->setEntity($object)->delete();
		}
		$json = new JsonModel();
        return $json;
	}
}
