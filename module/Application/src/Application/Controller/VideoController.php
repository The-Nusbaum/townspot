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

    public function playerAction()
    {
		$this->init();
		$videoId = $this->params()->fromRoute('id');
		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		if ($media = $mediaMapper->find($videoId)) {
			$searchIndex = new VideoIndex($this->getServiceLocator());
			$queries = array();
			$queries[] = 'user:"' . ($media->getUser()->getUserName()) . '"';
			foreach ($media->getCategories() as $category) {
				$queries[] = 'categories:"' . htmlentities($category->getName()) . '"';
			}
			$related = $searchIndex->find($queries);
			$related = array_diff($related,array($videoId));
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
	
    public function embedAction()
    {
		print "Embed";
		die;
	}

    public function uploadAction()
    {
		print "Upload";
		die;
	}
	
    public function deleteAction()
    {
		print "Delete";
		die;
	}
	
    public function relatedAction()
    {
		print "Related";
		die;
	}
	
    public function ratingsAction()
    {
		print "Ratings";
		die;
	}
	
    public function commentsAction()
    {
		print "Comments";
		die;
	}
	
    public function successAction()
    {
		print "Success";
		die;
	}
	
    public function flagAction()
    {
		print "Flag";
		die;
	}
	
    public function addratingAction()
    {
		print "Add Rating";
		die;
	}
	
    public function addcommentAction()
    {
		print "Add Comment";
		die;
	}
	
    public function removecommentAction()
    {
		print "Remove Comment";
		die;
	}
	
	
	
}
