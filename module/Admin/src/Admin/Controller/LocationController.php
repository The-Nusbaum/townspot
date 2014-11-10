<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LocationController extends AbstractActionController
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
    }
}
