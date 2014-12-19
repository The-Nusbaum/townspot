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

class StaticPageController extends AbstractActionController
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
			 ->appendName('og:title', 'Townspot.tv')
			 ->appendName('og:description', 'TownSpot.tv is the Local Video Network spotlighting local talent across the country through a curated video directory.')
			 ->appendName('og:site_name', 'townspot.tv')
			 ->appendName('og:url', 'http://www.townspot.tv/')
			 ->appendName('og:image', 'http://www.townspot.tv/img/townspotwhat.png')
			 ->appendName('twitter:card', 'summary')
			 ->appendName('twitter:title','Townspot.tv')
			 ->appendName('twitter:description', 'TownSpot.tv is the Local Video Network spotlighting local talent across the country through a curated video directory.')
			 ->appendName('twitter:image', 'http://www.townspot.tv/img/townspotwhat.png');
	}

    public function privacyAction()
    {
		$this->init();
    }

	public function aboutAction()
    {
		$this->init();
    }

    public function differentAction()
    {
		$this->init();
    }

    public function termsAction()
    {
		$this->init();
    }

    public function agreementAction()
    {
		$this->init();
    }

    public function standardsAction()
    {
		$this->init();
    }

    public function policyAction()
    {
		$this->init();
    }

    public function tipsAction()
    {
		$this->init();
    }
}
