<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SectionController extends AbstractActionController
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

    public function mediaAction()
    {
		$this->isAuthenticated();
		$section 		= $this->params()->fromRoute('section');	
		$SectionMapper 	= new \Townspot\SectionBlock\Mapper($this->getServiceLocator());
		$sectionMedia 	= $SectionMapper->getSectionMediaByBlockName($section);

		return new ViewModel(
			array(
				'section'		=> $section,
				'section_media'	=> $sectionMedia
			)
		);
    }
	
    public function updateAction()
    {
		$this->isAuthenticated();
    }
	
}
