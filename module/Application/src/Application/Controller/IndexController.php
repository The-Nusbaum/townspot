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
use \Application\Forms\ContactForm;

class IndexController extends AbstractActionController
{
	public function __construct() 
	{
	}
	
	public function init() 
	{
		$this->getServiceLocator()
			 ->get('ViewHelperManager')
			 ->get('HeadTitle')
			 ->set('TownSpot &bull; Your Town. Your Talent. Spotlighted');
	}

    public function indexAction()
    {
		$this->init();
		$SectionMapper = new \Townspot\SectionBlock\Mapper($this->getServiceLocator());
		$_onScreen = $SectionMapper->findOneByBlockName('On Screen');
		$_dailyHighlights = $SectionMapper->findOneByBlockName('Daily Highlights');
		$_staffFavorites = $SectionMapper->findOneByBlockName('Staff Favorites');
		
		$dailyHighlights = array();
		$staffFavorites = array();
		
		foreach ($_dailyHighlights->getSectionMedia() as $media) {
			$dailyHighlights[] = $media->getMedia();
		}
		foreach ($_staffFavorites->getSectionMedia() as $media) {
			$staffFavorites[] = $media->getMedia();
		}
        return new ViewModel(
			array(
				'onScreen' 			=> $_onScreen->getSectionMedia(),
				'dailyHighlights' 	=> $dailyHighlights,
				'staffFavorites' 	=> $staffFavorites,
			)
		);
    }
	
    public function contactAction()
    {
        $form = new ContactForm();
		$viewModel = new ViewModel(array(
			'form'      => $form
		));		

		if ($data = $this->params()->fromPost()) {
			$form->setData($data);
			if ($form->isValid()) {
				$sMessage  = "Name: " . $data['name'] . "\n";
				$sMessage .= "E-Mail: " . $data['email'] . "\n";
				$sMessage .= "Sent on: " . date('l M j, Y g:ia') . "\n";
				$sMessage .= "Subject: " . $data['subject'] . "\n";
				$sMessage .= "Message: " . $data['message'] . "\n";
				$mail = new \Zend\Mail\Message();
				$mail->setFrom($data['email'], $data['name']);
				$mail->addTo('info@townspot.tv', 'info@townspot.tv');
				$mail->setSubject('Contact Email - ' . $data['subject']);
				$mail->setBody($sMessage);

				if (APPLICATION_ENV == 'production') {
					$transport = new \Zend\Mail\Transport\Sendmail();
					$transport->send($mail);
				}
				$viewModel->setTemplate('application\index\contactsuccess.phtml');
			}
		}
        return $viewModel;
	}

    public function stageAction()
    {
		print "Stage";
		die;
	}
	
	
}
