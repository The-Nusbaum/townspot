<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController
{
    public function __construct()
    {
	}

    public function isAuthenticated()
    {
		if (!$this->zfcUserAuthentication()->hasIdentity()) {
			return $this->redirect()->toUrl('/');
		}
		if (!$this->isAllowed('admin')) {
			return $this->redirect()->toUrl('/');
		}
	}
	
    public function indexAction()
    {
		$this->isAuthenticated();
		$type = $this->params()->fromRoute('type');
		$provinceMapper = new \Townspot\Province\Mapper($this->getServiceLocator());
				
		return new ViewModel( 
			array(
				'type'			=> $type,
				'status'		=> $this->params()->fromQuery('status'),
				'provinces'		=> $provinceMapper->getProvincesHavingMedia(),
			)
		);
    }
	
    public function addAction()
    {
		$this->isAuthenticated();
		$type = $this->params()->fromRoute('type');
		
        $countryMapper = new \Townspot\Country\Mapper($this->getServiceLocator());
        $countries = $countryMapper->findAll();
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
                'email' => uniqid().'@example.com',
                'display_name' => $data['displayName']
            );
            $userService =  $this->getServiceLocator()->get('zfcuseruserservice');
            $user = $userService->register($regData);
            $country = $countryMapper->find($data['country_id']);
            $province = $provinceMapper->find($data['province_id']);
            $city = $cityMapper->find($data['city_id']);
            $user = $userMapper->find($user->getId());
            $roleMapper = new \Townspot\UserRole\Mapper($this->getServiceLocator());
            $role = $roleMapper->find('Artist');

            $user->setFirstName($data['firstName'])
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
                 ->addRole($role)
                 ->setEmailNotification($data['email_notifications']);

            if(!empty($data['email'])) {
                $user->setEmail($data['email']);
            } else {
                $user->setEmail('');
            }

            $userMapper->setEntity($user)->save();

            $this->redirect()->toRoute('admin-users');
        }
	
        $form = new \Application\Forms\User\Register('user',$countries,$provinces);
        $this->getServiceLocator()
            ->get('viewhelpermanager')
            ->get('HeadScript')->appendFile('/js/townspot.js')
							   ->appendFile('/js/userRegister.js');

		return new ViewModel( 
			array(
				'type'			=> $type,
				'form'			=> $form,
			)
		);
    }
	
    public function deleteAction()
    {
		$this->isAuthenticated();
    }
	
    public function editAction()
    {
		$this->isAuthenticated();
		$id = $this->params()->fromRoute('id');
		$type = $this->params()->fromRoute('type');

        $request = $this->getRequest();
        $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
        $user = $userMapper->findOneById($id);
		
        $countryMapper = new \Townspot\Country\Mapper($this->getServiceLocator());
        $countries = $countryMapper->findAll();

        $provinceMapper = new \Townspot\Province\Mapper($this->getServiceLocator());
        $provinces = $provinceMapper->findByCountry($user->getCountry());

        $cityMapper = new \Townspot\City\Mapper($this->getServiceLocator());
        $cities = $cityMapper->findByProvince($user->getProvince());
		
        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $country = $countryMapper->find($data['country_id']);
            $province = $provinceMapper->find($data['province_id']);
            $city = $cityMapper->find($data['city_id']);
			$userroleMapper = new \Townspot\UserRole\Mapper($this->getServiceLocator());
			$userrole = $userroleMapper->findOneById($data['role_id']);
			$user->removeRoles();
            $user->setArtistName($data['artistName'])
                 ->setFirstName($data['firstName'])
                 ->setLastName($data['lastName'])
                 ->setCountry($country)
                 ->setProvince($province)
                 ->setCity($city)
                 ->setWebsite($data['website'])
                 ->setAboutMe($data['aboutMe'])
                 ->setInterests($data['interests'])
                 ->setDescriptions($data['description'])
                 ->setAllowContact($data['allow_contact'])
                 ->setTermsAgreement(@$data['terms_agreement'])
                 ->setEmailNotification(@$data['email_notifications'])
				 ->addRole($userrole);
			if (isset($data['image_url'])) {
				$user->setImageUrl($data['image_url']);
			}
            $userMapper->setEntity($user)->save();
            $this->redirect()->toRoute('admin-users');
		}
				
		
        $form = new \Admin\Forms\User\Edit('user',$countries,$provinces,$cities);

        $userData = $user->toArray();
        $userData['user_id'] = $userData['id'];

		$role = 'User';
		foreach ($user->getRoles() as $_role) {
			if ($role == 'User') {
				if ($_role->getId() == 'Artist') {
					$role = 'Artist';
				} 
				if ($_role->getId() == 'Administrator') {
					$role = 'Administrator';
				} 
			}
		}
		$userData['role_id'] = $role;
		
        $form->setData($userData);
        $form->setData($user->toArray());
		
        $this->getServiceLocator()
            ->get('viewhelpermanager')
            ->get('HeadScript')->appendFile('/js/townspot.js')
							   ->appendFile('/js/adminUserEdit.js');

 	    return new ViewModel( 
			array(
				'type'			=> $type,
				'id'			=> $id,
				'form'			=> $form,
			)
		);
    }
	
    public function showAction()
    {
		$this->isAuthenticated();
		$id = $this->params()->fromRoute('id');
		$type = $this->params()->fromRoute('type');
		$referrer = $this->params()->fromQuery('referrer');
		$userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
		$user 	= $userMapper->find($id);
		$artist = false;
		$type   = 'User';
		if ($user->hasRole('Artist')) {
			$artist = true;
			$type	= 'Artist';
		} elseif ($user->hasRole('Administrator')) {
			$type	= 'Administrator';
			if (count($this->getMedia()) > 0) {
				$artist = true;
			}
		}
		return new ViewModel( 
			array(
				'user'			=> $user,
				'type'			=> $type,
				'artist'		=> $artist,
				'referrer'	=> $referrer,
			)
		);
    }
}
