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
		$SectionMapper = new \Townspot\SectionBlock\Mapper($this->getServiceLocator());
		$_onScreen = $SectionMapper->findOneByBlockName('On Screen');
		$_dailyHighlights = $SectionMapper->findOneByBlockName('Daily Highlights');
		$_staffFavorites = $SectionMapper->findOneByBlockName('Staff Favorites');
		$dailyHighlights = array();
		$staffFavorites = array();
		foreach ($_dailyHighlights->getSectionMedia() as $media) { $dailyHighlights[] = $media->getMedia()->getId(); }
		foreach ($_staffFavorites->getSectionMedia() as $media) { $staffFavorites[] = $media->getMedia()->getId(); }
		
        return new ViewModel(
			array(
				'onScreen' 			=> $_onScreen->getSectionMedia(),
				'dailyHighlights' 	=> $dailyHighlights,
				'staffFavorites' 	=> $staffFavorites,
			)
		);
    }
	
    public function stageAction()
    {
		print "Stage";
		die;
	}
}
