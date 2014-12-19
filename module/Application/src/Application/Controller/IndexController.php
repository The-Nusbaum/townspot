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
		$this->getServiceLocator()
			 ->get('ViewHelperManager')
			 ->get('HeadMeta')
			 ->appendProperty('og:title', 'Townspot.tv')
			 ->appendProperty('og:description', 'TownSpot.tv is the Local Video Network spotlighting local talent across the country through a curated video directory.')
			 ->appendProperty('og:site_name', 'townspot.tv')
			 ->appendProperty('og:url', 'http://www.townspot.tv/')
			 ->appendProperty('og:image', 'http://www.townspot.tv/img/townspotwhat.png')
			 ->appendProperty('twitter:card', 'summary')
			 ->appendProperty('twitter:title','Townspot.tv')
			 ->appendProperty('twitter:description', 'TownSpot.tv is the Local Video Network spotlighting local talent across the country through a curated video directory.')
			 ->appendProperty('twitter:image', 'http://www.townspot.tv/img/townspotwhat.png');
	}

    public function indexAction()
    {
		$this->init();
        $cache                      = $this->getServiceLocator()->get('cache-general');        
		$cache->clearExpired();
		
        if ($results = $cache->getItem('home')) {
			return new ViewModel($results);
		}

		$SectionMapper 		= new \Townspot\SectionBlock\Mapper($this->getServiceLocator());
		$onScreen 			= $SectionMapper->getSectionMediaByBlockName('On Screen');
		$dailyHighlights 	= $SectionMapper->getSectionMediaByBlockName('Daily Highlights');
		$staffFavorites 	= $SectionMapper->getSectionMediaByBlockName('Staff Favorites');

		$results			= array(
			'onScreenCount'		=> count($onScreen),
			'onScreen' 			=> json_encode($onScreen),
			'dailyHighlights' 	=> json_encode($dailyHighlights),
			'staffFavorites' 	=> json_encode($staffFavorites),
		);
		$cache->setItem('home', $results);
		
		return new ViewModel($results);
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
