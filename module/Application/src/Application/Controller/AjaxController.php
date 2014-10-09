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

class AjaxController extends AbstractActionController
{
	public function __construct() 
	{
	}
	
	public function init() 
	{
	}

    public function explorelinkAction()
    {
		$response = array('FullName'	=> '',
						  'Link'		=> ''
		);
		$this->init();
		$request = $this->getRequest();
		$lat = 0;
		$long = 0;
        if ($request->isPost()) {
			if ($coords = $request->getPost()->get('coords')) {
				list($lat,$long) = explode(',',$coords);
			} else {
				$record = $this->getServiceLocator()->get('geoip')->getRecord();
				$lat = $record->getLatitude();
				$long = $record->getLongitude();
			}
			if (($lat)&&($long)) {
				$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
				$media = $mediaMapper->getClosest($lat,$long);
				$response = array('FullName'	=> $media->getCity()->getFullName(),
								  'Link'		=> $media->getCity()->getDiscoverLink()
				);
			}
		}
		die(json_encode($response));
    }
}
