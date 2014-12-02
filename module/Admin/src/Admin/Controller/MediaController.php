<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MediaController extends AbstractActionController
{
    public function __construct()
    {
	}

    public function isAuthenticated()
    {
		if (!$this->zfcUserAuthentication()->hasIdentity()) {
			return $this->redirect()->toUrl('/');
		}
		if (!$this->isAllowed('admin')) {
			return $this->redirect()->toUrl('/');
		}
	}
	
    public function indexAction()
    {
		$this->isAuthenticated();
		$provinceMapper = new \Townspot\Province\Mapper($this->getServiceLocator());
		return new ViewModel( 
			array(
				'type'			=> $this->params()->fromQuery('approved'),
				'provinces'		=> $provinceMapper->getProvincesHavingMedia(),
			)
		);
    }
	
    public function addAction()
    {
		$this->isAuthenticated();
    }

    public function deleteAction()
    {
		$this->isAuthenticated();
    }
	
    public function editAction()
    {
		$this->isAuthenticated();
    }
	
    public function showAction()
    {
		$this->isAuthenticated();
		$id = $this->params()->fromRoute('id');
		$referrer = $this->params()->fromQuery('referrer');
		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		
		return new ViewModel( 
			array(
				'media'		=> $mediaMapper->find($id),
				'referrer'	=> $referrer,
			)
		);
    }
}
