<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class IndexController extends AbstractActionController
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
		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		$userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
		$stats['media'] = $mediaMapper->getStats();
		$stats['user'] = $userMapper->getStats();
		$stats['views'] = $mediaMapper->getTopStats();
		$stats['artist'] = $userMapper->getTopArtistStats();
		$stats['comments'] = $userMapper->getTopCommenterStats();
		$loggedInUser = $userMapper->find($this->zfcUserAuthentication()->getIdentity()->getId());
		return new ViewModel( 
			array(
				'stats'			=> $stats,
				'loggedInUser'	=> $loggedInUser,
			)
		);
    }
}
