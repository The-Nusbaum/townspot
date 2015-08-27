<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Symfony\Component\Console\Application;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Factory;
use Zend\Config;
use Zend\Http\Request;
use DoctrineModule\Authentication\Adapter\ObjectRepository as ObjectRepositoryAdapter;
use ZendService\Twitter\Twitter;
use ZendOAuth;
use Zend\Mail;

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
        if($query->rowCount())$this->_view->setVariable('twitter', $query->fetchAll()[0]);
        else $this->_view->setVariable('twitter', array());

        $this->_view->setVariable('authdUser', $user);
        $this->_view->setVariable('user', $user);
        $this->_view->setVariable('canEdit', true);
        $this->_view->setVariable('TZoffset', 0);
        $this->_view->setVariable('', true);
        return $this->_view;
    }

    public function profileAction()
    {
        $this->_view->setTemplate('application/user/index');
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
        $this->_view->setVariable('twitter', $query->rowCount()?$query->fetchAll()[0]:array());

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
    protected function _process($values)
    {
    // Get our authentication adapter and check credentials
        $adapter = $this->_getAuthAdapter();
        $adapter->setIdentity($values['username']);
        $adapter->setCredential($values['password']);

        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($adapter);
        if ($result->isValid()) {
            $user = $adapter->getResultRowObject();
            $auth->getStorage()->write($user);
            return true;
        }
        return false;
    }

    protected function _getAuthAdapter() {

        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

        $authAdapter->setTableName('users')
            ->setIdentityColumn('username')
            ->setCredentialColumn('password')
            ->setCredentialTreatment('SHA1(CONCAT(?,salt))');

        return $authAdapter;
    }

    public function loginAction() {
        //this is really hacky, but time is short and this will brute force it

    }

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
        $user->setPassword(null);

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
        $country = $countryMapper->find(99);

        $provinceMapper = new \Townspot\Province\Mapper($this->getServiceLocator());
        $provinces = $provinceMapper->findByCountry(99);

        $cityMapper = new \Townspot\City\Mapper($this->getServiceLocator());
        if($this->getRequest()->isPost()) {
            $user = new \Townspot\User\Entity();
            $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());

            $data = $this->params()->fromPost();

            if(empty($data['displayName'])) {
                if(!empty($data['artistName'])) {
                    $data['displayName'] = $data['artistName'];
                } else {
                    $data['displayName'] = $data['username'];
                }
            }
            $regData = array(
                'username' => $data['username'],
                'password' => $data['password'],
                'passwordVerify' => $data['password2'],
                'email' => $data['email'],
                'display_name' => $data['displayName']
            );
            $userService =  $this->getServiceLocator()->get('zfcuseruserservice');
            $user = $userService->register($regData);

            $province = $provinceMapper->find($data['province_id']);
            $city = $cityMapper->find($data['city_id']);

            $user = $userMapper->find($user->getId());
            $user->setArtistName($data['artistName'])
                ->setFirstName($data['firstName'])
                ->setLastName($data['lastName'])
                ->setCountry($country)
                ->setProvince($province)
                ->setCity($city)
                ->setWebsite($data['website'])
                ->setImageUrl($data['image_url'])
                ->setAboutMe($data['aboutMe'])
                ->setInterests($data['interests'])
                ->setDescriptions($data['description'])
                ->setAllowContact($data['allow_contact'])
                ->setTermsAgreement($data['terms_agreement'])
                ->setEmailNotification($data['email_notifications']);

            $userMapper->setEntity($user)->save();

            $this->flashMessenger()->addMessage('Please check your email for activation instructions');
            $this->redirect()->toRoute('zfcuser-login');

        }

        $form = new \Application\Forms\User\Register('user',array(),$provinces);
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

    public function forgotPasswordAction() {
        $form = new \Application\Forms\User\ForgotPassword();
        $this->_view->setVariable('form',$form);
        return $this->_view;
    }

    public function resetSentAction() {
        $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());

        if($this->getRequest()->isPost()) {
            $username = $this->params()->fromPost('username');
            $user = $userMapper->findByUsernameOrEmail($username);

            if($user instanceof \Townspot\User\Entity) {
                $key = uniqid();
                $sMessage  = "Please follow the link below to verify your account and reset your password\n\n";
                $sMessage .= "http://townspot.tv/verify/" . $key . "\n";
                $mail = new Mail\Message();
                $mail->setBody($sMessage);
                $mail->setFrom('webmaster@townspot.tv', 'Townspot.tv');
                $mail->addTo($user->getEmail(), $user->getFirstName().' '.$user->getLastName());
                $mail->addBcc('emailcopy@townspot.tv');
                $mail->setSubject('Forgot Password');

                $transport = new Mail\Transport\Sendmail();
                $transport->send($mail);

                $user->setSecurityKey($key);
                $userMapper->setEntity($user)->save();
            }
        }
        return $this->_view;
    }

    public function verifyAction() {
        $key = $this->params()->fromRoute('key');

        if(!$key) {
            $this->flashMessenger()->addMessage('No Security Key Provided');
            $this->redirect()->toUrl('/user/login');
        }

        $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
        $user = $userMapper->findOneBy(array('_security_key'=>$key));

        if($user instanceof \Townspot\User\Entity) {
            $form = new \Application\Forms\User\ChangePassword();
            $form->get('user_id')->setValue($user->getId());
            $this->_view->setVariable('form',$form);

            return $this->_view;
        }

        $this->flashMessenger()->addMessage('No user found or the security token is expired/invalid');
        $this->redirect()->toUrl('/user/login');


    }

    public function changePasswordAction() {
        if($this->getRequest()->isPost()) {
            $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
            $user = $userMapper->find($this->params()->fromPost('user_id'));
            $pw = $this->params()->fromPost('password');
            $pw2 = $this->params()->fromPost('password2');

            if(!empty($pw) && !empty($pw2) && $pw == $pw2 && preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,15}$/',$pw)) {
                $user->setPassword($pw)
                    ->setSecurityKey(null);
                $userMapper->setEntity($user)->save();
                $this->flashMessenger()->addMessage('Password Changed, you may now login');
                $this->redirect()->toUrl('/user/login');
            } else {
                $this->flashMessenger()->addMessage('Please enter a valid password');
                $this->redirect()->toUrl('/verify/'.$user->getSecurityKey());
                return;
            }
        }
        $this->redirect()->toUrl('/user/login');
    }
}
