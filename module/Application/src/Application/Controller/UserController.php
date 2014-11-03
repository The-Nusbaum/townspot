<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Album\Form\EditForm;
use Symfony\Component\Console\Application;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Factory;
use Zend\Config;
use Zend\Http\Request;
use DoctrineModule\Authentication\Adapter\ObjectRepository as ObjectRepositoryAdapter;
use ZendService\Twitter\Twitter;
use ZendOAuth;

class UserController extends AbstractActionController
{
    public function __construct()
    {
        $this->_view = new ViewModel();
        $this->auth = new \Zend\Authentication\AuthenticationService();
        $this->_view->setVariable('authdUser',$this->auth->getIdentity());
    }

    public function indexAction()
    {
        $this->getServiceLocator()
            ->get('ViewHelperManager')
            ->get('HeadTitle')
            ->set('TownSpot &bull; Your Town. Your Talent. Spotlighted');

        $this->getServiceLocator()
            ->get('viewhelpermanager')
            ->get('HeadScript')->appendFile('/js/townspot.js');

        $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
        $user = $userMapper->findOneById($this->auth->getIdentity());
        $this->_view->setVariable('user',$user);
        $this->_view->setVariable('canEdit',true);
        $this->_view->setVariable('TZoffset',0);
        $this->_view->setVariable('',true);
        return $this->_view;
    }

    public function loginAction() {}

    public function logoutAction() {
        $this->auth->clearIdentity();
        $this->flashMessenger()->addMessage('You have been logged out');
        $this->redirect()->toRoute('login');
    }

    public function editAction() {
        $request = $this->getRequest();
        $post = $request->getPost();

        $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
        $user = $userMapper->findOneById($this->auth->getIdentity());

        $countryMapper = new \Townspot\Country\Mapper($this->getServiceLocator());
        $countries = $countryMapper->findAll();

        $provinceMapper = new \Townspot\Province\Mapper($this->getServiceLocator());
        $provinces = $provinceMapper->findByCountry($user->getCountry());

        $cityMapper = new \Townspot\City\Mapper($this->getServiceLocator());
        $cities = $cityMapper->findByProvince($user->getProvince());

        $form = new \Application\Forms\User\Edit('user',$countries,$provinces,$cities);
        $userData = $user->toArray();
        $userData['user_id'] = $userData['id'];
        $form->setData($userData);

        $form->setData($user->toArray());
        $this->_view->setVariable('form', $form);

        $this->getServiceLocator()
            ->get('viewhelpermanager')
            ->get('HeadScript')->appendFile('/js/userEdit.js');


        return $this->_view;
    }

    public function manageseriesAction() {
        $this->getServiceLocator()
            ->get('viewhelpermanager')
            ->get('HeadScript')->appendFile('/js/manageseries.js');

        $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
        $user = $userMapper->findOneById($this->auth->getIdentity());
        $this->_view->setVariable('user',$user);
        return $this->_view;
    }
}