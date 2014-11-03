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
        $this->_view = new ViewModel();
        $this->auth = new \Zend\Authentication\AuthenticationService();
        $this->_view->setVariable('authdUser',$this->auth->getIdentity());
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
        $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
        $user = $userMapper->findOneById($this->auth->getIdentity());

        $countryMapper = new \Townspot\Country\Mapper($this->getServiceLocator());
        $countries = $countryMapper->findAll();

        $provinceMapper = new \Townspot\Province\Mapper($this->getServiceLocator());
        $provinces = $provinceMapper->findByCountry($user->getCountry());

        $cityMapper = new \Townspot\City\Mapper($this->getServiceLocator());
        $cities = $cityMapper->findByProvince($user->getProvince());

        $categoryMapper = new \Townspot\Category\Mapper($this->getServiceLocator());
        $categories = $categoryMapper->findByParent(0);

        $headForm = new \Application\Forms\Video\Upload\HeadForm('uploadHeader', $countries, $provinces, $cities);

        $data = array(
            'user_id' => $user->getId(),
            'country_id' => $user->getCountry()->getId(),
            'province_id' => $user->getProvince()->getId(),
            'city_id' => $user->getCity()->getId()
        );

        $headForm->setData($data);
        $this->_view->setVariable('headForm',$headForm);

        $ytForm = new \Application\Forms\Video\Upload\YtForm();
        $this->_view->setVariable('ytForm',$ytForm);

        $manualForm = new \Application\Forms\Video\Upload\ManualForm('manualForm');
        $this->_view->setVariable('manualForm',$manualForm);

        $footerForm = new \Application\Forms\Video\Upload\FooterForm('footerForm');
        $this->_view->setVariable('footerForm',$footerForm);

        $this->getServiceLocator()
            ->get('viewhelpermanager')
            ->get('HeadScript')->appendFile('/js/upload.js');

		return $this->_view;
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
