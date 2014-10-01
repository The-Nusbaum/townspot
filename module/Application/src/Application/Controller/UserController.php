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
            ->get('HeadScript')->appendFile('/js/userProfile.js');
        $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
        $user = $userMapper->findOneById($this->auth->getIdentity());
        $this->_view->setVariable('user',$user);
        $this->_view->setVariable('canEdit',true);
        $this->_view->setVariable('TZoffset',0);
        $this->_view->setVariable('',true);
        return $this->_view;
    }

    public function loginAction() {

    }

    public function logoutAction() {
        $this->auth->clearIdentity();
        $this->flashMessenger()->addMessage('You have been logged out');
        $this->redirect()->toRoute('login');
    }

    public function editAction() {

    }
}