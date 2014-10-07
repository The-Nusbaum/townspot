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
use \Townspot\Lucene\VideoIndex;

class VideoController extends AbstractActionController
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
		$videoId = $this->params()->fromRoute('param1');
		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		if ($media = $mediaMapper->find($videoId)) {
			$searchIndex = new VideoIndex($this->getServiceLocator());
			$queries = array();
			$queries[] = 'user:"' . ($media->getUser()->getUserName()) . '"';
			foreach ($media->getCategories() as $category) {
				$queries[] = 'categories:"' . htmlentities($category->getName()) . '"';
			}
			$related = array();
			foreach ($queries as $query) {
				$matches = $searchIndex->getIndex()->find($query);
				foreach ($matches as $hit) {	
					if ($hit->mediaid != $videoId) {
						$related[] = $hit->mediaid; 
					}
				}
			}
			$related = array_unique($related);
			return new ViewModel(
				array(
					'media_id' => $videoId,
					'media'    => $media,
					'related'  => array_slice($related, 0, 3)
				)
			);
		} else {
			//Error
		}
		return null;
    }
}
