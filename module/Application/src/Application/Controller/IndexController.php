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
		$onScreen = $SectionMapper->findOneByBlockName('On Screen');
		$dailyHighlights = $SectionMapper->findOneByBlockName('Daily Highlights');
		$staffFavorites = $SectionMapper->findOneByBlockName('Staff Favorites');
        return new ViewModel(
			array(
				'onScreen' 			=> $onScreen->getSectionMedia(),
				'dailyHighlights' 	=> $dailyHighlights->getSectionMedia(),
				'staffFavorites' 	=> $staffFavorites->getSectionMedia(),
			)
		);
		
        return new ViewModel();
    }
}
