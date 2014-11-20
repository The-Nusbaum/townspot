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
        $this->_view->setVariable('authdUser', $this->auth->getIdentity());
    }

    public function indexAction()
    {
        $this->getServiceLocator()
            ->get('ViewHelperManager')
            ->get('HeadTitle')
            ->set('TownSpot &bull; Your Town. Your Talent. Spotlighted');

        $this->getServiceLocator()
            ->get('viewhelpermanager')
            ->get('HeadScript')->appendFile('/js/townspot.js')
            ->appendFile('/js/userProfile.js');

        $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
        $user = $userMapper->findOneById($this->auth->getIdentity());
        //i HATE doing it this way... but whatevs
        $sql = "select * from user_social_media where source = 'twitter' and user_id = :uid";
        $query = $userMapper->getEntityManager()->getConnection()->prepare($sql);
        $query->execute(array(':uid' => $user->getId()));
        $this->_view->setVariable('twitter', $query->fetchAll()[0]);

        $this->_view->setVariable('authdUser', $user);
        $this->_view->setVariable('user', $user);
        $this->_view->setVariable('canEdit', true);
        $this->_view->setVariable('TZoffset', 0);
        $this->_view->setVariable('', true);
        return $this->_view;
    }

    public function profileAction()
    {
        $this->_view->setTemplate('Application/user/index');
        $username = $this->params()->fromRoute('username');
        $id = $this->params()->fromRoute('id');
        $this->getServiceLocator()
            ->get('ViewHelperManager')
            ->get('HeadTitle')
            ->set('TownSpot &bull; Your Town. Your Talent. Spotlighted');

        $this->getServiceLocator()
            ->get('viewhelpermanager')
            ->get('HeadScript')->appendFile('/js/townspot.js')
            ->appendFile('/js/userProfile.js');

        $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
        if($id) {
            $user = $userMapper->find($id);
        } else {
            $user = $userMapper->findOneByUsername($username);
        }
        //i HATE doing it this way... but whatevs
        $sql = "select * from user_social_media where source = 'twitter' and user_id = :uid";
        $query = $userMapper->getEntityManager()->getConnection()->prepare($sql);
        $query->execute(array(':uid' => $user->getId()));
        $this->_view->setVariable('twitter', $query->fetchAll()[0]);

        if($this->auth->getIdentity()) {
            $authdUser = $userMapper->find($this->auth->getIdentity());
        } else {
            $authdUser = false;
        }

        $this->_view->setVariable('authdUser', $authdUser);
        $this->_view->setVariable('user', $user);
        $this->_view->setVariable('canEdit', false);
        $this->_view->setVariable('TZoffset', 0);
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

        $sql = "select * from user_oauth where user_id = :uid";
        $query = $userMapper->getEntityManager()->getConnection()->prepare($sql);
        $query->execute(array(':uid'=> $user->getId()));
        $oauths = $query->fetchAll();

        foreach($oauths as $oauth) {
            $el = $form->get("link_{$oauth['source']}");
            $label = ucfirst($oauth['source']);
            $el->setName("unlink_{$oauth['source']}")
                ->setAttribute('label',"Unlink $label");
        }
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

    public function unlinkAction() {
        $provider = $this->params()->fromRoute('provider');
        $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
        $user = $userMapper->findOneById($this->auth->getIdentity());

        $sql = "delete from user_oauth where user_id = :uid and source = :provider ";
        $query = $userMapper->getEntityManager()->getConnection()->prepare($sql);
        $query->execute(array(':uid'=> $user->getId(), ':provider' => $provider));

        if($provider == 'twitter') {
            $sql = "delete from user_social_media where user_id = :uid and source = 'twitter' ";
            $query = $userMapper->getEntityManager()->getConnection()->prepare($sql);
            $query->execute(array(':uid' => $user->getId()));
        }

        $this->redirect()->toRoute('dashboard');
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

    public function registerAction() {
        $countryMapper = new \Townspot\Country\Mapper($this->getServiceLocator());
        $countries = $countryMapper->findAll();

        $provinceMapper = new \Townspot\Province\Mapper($this->getServiceLocator());
        $provinces = $provinceMapper->findByCountry(99);

        $form = new \Application\Forms\User\Register('user',$countries,$provinces);
        $this->_view->setVariable('form',$form);

        $this->getServiceLocator()
            ->get('viewhelpermanager')
            ->get('HeadScript')->appendFile('/js/userRegister.js');

        return $this->_view;
    }

    public function linkAction() {
        $provider = $this->params()->fromRoute('provider');
        $oauth_callback = $this->params()->fromRoute('oauth_callback');
        $opauth_service = $this->getServiceLocator()->get('opauth_service');

        // set custom login and callback routes
        $opauth_service->setLoginUrlName('custom_lfjopauth_login');
        $opauth_service->setCallbackUrlName('custom_lfjopauth_callback');

        return $opauth_service->redirect($provider, 'custom_lfjopauth_callback');
    }

    public function linkCallbackAction() {
        $opauth = $_SESSION['opauth'];

        $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
        $user = $userMapper->findOneById($this->auth->getIdentity());

        $oauthMapper = new \Townspot\UserOauth\Mapper($this->getServiceLocator());
        $socialMapper = new \Townspot\UserSocialMedia\Mapper($this->getServiceLocator());

        $oauth = new \Townspot\UserOauth\Entity();
        $oauth->setUser($user)
            ->setExternalId($opauth['auth']['uid'])
            ->setSource(strtolower($opauth['auth']['provider']));
        $oauthMapper->setEntity($oauth)->save();

        if($opauth['auth']['provider'] == 'Twitter') {
            $social = new \Townspot\UserSocialMedia\Entity();
            $social->setUser($user)
                ->setSource('twitter')
                //->setLink('iamiandotme');
                ->setLink($opauth['auth']['info']['nickname']);
            $socialMapper->setEntity($social)->save();
        }

        $this->redirect()->toRoute('dashboard');
    }

    public function sendEmail() {

    }
}