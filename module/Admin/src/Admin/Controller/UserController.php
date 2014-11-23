<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController
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
		$type = $this->params()->fromRoute('type');
		$provinceMapper = new \Townspot\Province\Mapper($this->getServiceLocator());
				
		return new ViewModel( 
			array(
				'type'			=> $type,
				'status'		=> $this->params()->fromQuery('status'),
				'provinces'		=> $provinceMapper->getProvincesHavingMedia(),
			)
		);
    }
	
    public function addAction()
    {
		$this->isAuthenticated();
		$type = $this->params()->fromRoute('type');
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
    }
}
