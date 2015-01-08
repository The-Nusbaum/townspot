<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MediaController extends AbstractActionController
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
		$provinceMapper = new \Townspot\Province\Mapper($this->getServiceLocator());
		return new ViewModel( 
			array(
				'type'			=> $this->params()->fromQuery('approved'),
				'provinces'		=> $provinceMapper->getProvincesHavingMedia(),
			)
		);
    }
	
    public function reviewAction()
    {
        $request = $this->getRequest();
        $data = $request->getPost();

        $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
        $user = $userMapper->find($data->get('user_id'));

        $countryMapper = new \Townspot\Country\Mapper($this->getServiceLocator());

        $provinceMapper = new \Townspot\Province\Mapper($this->getServiceLocator());
        $province = $provinceMapper->find($data->get('province_id'));
        $country = $province->getCountry();

        $cityMapper = new \Townspot\City\Mapper($this->getServiceLocator());
        $city = $cityMapper->find($data->get('city_id'));

        $categoryMapper = new \Townspot\Category\Mapper($this->getServiceLocator());
        $categories = $categoryMapper->findAll();
        foreach($categories as $c) {
            $allCategories[$c->getId()] = $c->getName();
        }

        if($this->getRequest()->isPost()){
            if($data->get('youtube_url') && !$data->get('review_ok')) {
                $client = new \Zend\Http\Client('https://example.org', array(
                    'adapter' => 'Zend\Http\Client\Adapter\Curl'
                ));
                $client->getAdapter()->setCurlOption(CURLOPT_SSL_VERIFYPEER, false);
                $yt = new \ZendGData\YouTube(
                    $client,
                    'Townspot',
                    "872367745273-sbsiuc81kh9o70ok3macc15d2ebpl440.apps.googleusercontent.com",
                    "AI39si7sp4rb57_29xMFWO2AoT8DDc0dKYklw7_IsUYpEhsxSL-DNK60f3eIF7OK_Iy0_xYm1eAxX2skGO57B6oHd6qJHHiPZA"

                );

                preg_match('/v=([^&]*)/i',$data->get('youtube_url'),$idMatches);
                if(!empty($idMatches[1])) {
                    $id = $idMatches[1];
                } else {
                    //no id
                }

                $ytVideo = $yt->getVideoEntry($id);
                $data->set('title',$ytVideo->getTitle())
                    ->set('description',$ytVideo->getVideoDescription())
                    ->set('duration',$ytVideo->getVideoDuration())
                    ->set('on_media_server',1)
                    ->set('preview_url',$ytVideo->getVideoThumbnails()[3]['url'])
                    ->set('source','youtube')
                    ->set('video_url',$data->get('youtube_url'));

            } elseif(!$data->get('review_ok')) {
                //do nothing?
            } else {
                $mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
				if ($data['media_id']) {
	                $mediaEntity = $mediaMapper->find($data['media_id']);
				} else {
	                $mediaEntity = new \Townspot\Media\Entity();
				}
                $mediaEntity->setUser($user)
							->setCountry($country)
							->setProvince($province)
							->setCity($city)
							->setTitle($data->get('title'))
							->setLogline($data->get('logline'))
							->setDescription($data->get('description'))
							->setUrl($data->get('video_url'))
							->setPreviewImage($data->get('preview_url'))
							->setAuthorised($data->get('authorised'))
							->setAllowContact($data->get('allow_contact'))
							->setSource($data->get('source'))
							->setDuration($data->get('duration'));

                $categoryMapper = new \Townspot\Category\Mapper($this->getServiceLocator());
                foreach($data->get('selCat') as $vc) {
                    $cat = $categoryMapper->find($vc);
                    $mediaEntity->addCategory($cat);
                }

                $mediaMapper->setEntity($mediaEntity);
                $mediaMapper->save();

                $this->flashMessenger()->addMessage('Video upload was successful.');

                return $this->redirect()->toRoute('admin-video');
            }
        }
		
		return new ViewModel( 
			array(
				'data'			=> $data,
				'state'			=> $province->getName(),
				'city'			=> $city->getName(),
				'allCategories'	=> $allCategories
			)
		);
	}
	
    public function addAction()
    {
		$this->isAuthenticated();

        $countryMapper = new \Townspot\Country\Mapper($this->getServiceLocator());
        $countries = $countryMapper->findAll();
		$country = $countryMapper->find(99);

        $provinceMapper = new \Townspot\Province\Mapper($this->getServiceLocator());
        $provinces = $provinceMapper->findByCountry($country);

        $categoryMapper = new \Townspot\Category\Mapper($this->getServiceLocator());
        $categories = $categoryMapper->findByParent(0);
		
		$userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
		$_users = $userMapper->findAll();
		$users = array();
		foreach ($_users as $user) {
			if (($user->hasRole('Administrator'))||($user->hasRole('Artist'))) {
				$users[] = $user;
			}
		}
		
        $headForm = new \Admin\Forms\Video\Upload\HeadForm('uploadHeader', $users, $countries, $provinces, array());
        $ytForm = new \Application\Forms\Video\Upload\YtForm();
        $manualForm = new \Application\Forms\Video\Upload\ManualForm('manualForm');
        $footerForm = new \Application\Forms\Video\Upload\FooterForm('footerForm');
        $videoMediaForm = new \Application\Forms\Video\Upload\VideoMediaForm('videoMediaForm');


        $this->getServiceLocator()
            ->get('viewhelpermanager')
            ->get('HeadScript')->appendFile('/js/townspot.js')
							   ->appendFile('/js/adminupload.js');

		return new ViewModel( 
			array(
				'headForm'			=> $headForm,
				'ytForm'			=> $ytForm,
				'manualForm'		=> $manualForm,
				'footerForm'		=> $footerForm,
				'videoMediaForm'	=> $videoMediaForm,
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
        $mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
        $media = $mediaMapper->find($id);

        $countryMapper = new \Townspot\Country\Mapper($this->getServiceLocator());
		$provinceMapper = new \Townspot\Province\Mapper($this->getServiceLocator());
        $cityMapper = new \Townspot\City\Mapper($this->getServiceLocator());
        $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
		
        //this tells the IDE what class we have as well as checks for a non-empty object
        if($media instanceof \Townspot\Media\Entity) {
            if(!$this->getRequest()->isPost()) {
				$_users = $userMapper->findAll();
				$users = array();
				foreach ($_users as $_user) {
					if (($_user->hasRole('Administrator'))||($_user->hasRole('Artist'))) {
						$users[] = $_user;
					}
				}
                $user = $userMapper->findOneById($media->getUser()->getId());
                $countries = $countryMapper->findAll();
                $provinces = $provinceMapper->findByCountry(99);
                var_dump($provinces->toArray());
                $cities = $cityMapper->findByProvince($media->getProvince());
				
                $categoryMapper = new \Townspot\Category\Mapper($this->getServiceLocator());
                $categories = $categoryMapper->findByParent(0);
				
				$headForm = new \Admin\Forms\Video\Upload\HeadForm('uploadHeader', $users, $countries, $provinces, $cities);
				$headForm->setSubForm(true);
				
                $data = $media->toArray();
                $data['selectedCategories'] = $this->_tierCats($media->getCategories());
                if ($data['source'] == 'youtube') $data['youtube_url'] = $data['url'];
                $headForm->setData($data);

                $mainForm = new \Application\Forms\Video\EditForm('manualForm');
				$mainForm->setSubForm(true);
                $mainForm->setData($data);

                $videoMediaForm = new \Application\Forms\Video\Upload\VideoMediaForm('videoMediaForm');
				$videoMediaForm->setSubForm(true);
                $videoMediaForm->setData($data);

                $this->getServiceLocator()
                    ->get('viewhelpermanager')
                    ->get('HeadScript')->appendFile('/js/townspot.js')
								       ->appendFile('/js/adminEdit.js');
				return new ViewModel( 
					array(
						'headForm'			=> $headForm,
						'mainForm'			=> $mainForm,
						'videoMediaForm'	=> $videoMediaForm,
						'data'				=> $data,
					)
				);
									   
            } else {
				$data = $this->params()->fromPost();
                foreach($data as $field => $value) {
                    if(method_exists($media,"set".ucfirst($field))) {
                        $method = "set".ucfirst($field);
                        $media->$method($value);
                    }
                }
				$province = $provinceMapper->find($data['province_id']);
				$media->setProvince($province);
				$city = $cityMapper->find($data['city_id']);
				$media->setCity($city);
				$user = $userMapper->find($data['user_id']);
				$media->setUser($user);
				
                foreach($media->getCategories() as $key => $cat) {
                    $media->removeCategory($key);
                }
                $mediaMapper->setEntity($media)->save();
                $categoryMapper = new \Townspot\Category\Mapper($this->getServiceLocator());
                foreach($this->params()->fromPost('selCat') as $cat_id) {
                    $cat = $categoryMapper->find($cat_id);
                    $media->addCategory($cat);
                }
                $mediaMapper->setEntity($media)->save();
                $this->flashMessenger()->addMessage('The video has been edited.');

				return $this->redirect()->toRoute('admin-video');
            }
        } else {
            //throw exception and 404
        }
    }
	
    public function showAction()
    {
		$this->isAuthenticated();
		$id = $this->params()->fromRoute('id');
		$referrer = $this->params()->fromQuery('referrer');
		$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
		
		return new ViewModel( 
			array(
				'media'		=> $mediaMapper->find($id),
				'referrer'	=> $referrer,
			)
		);
    }
	
    protected function _tierCats($cats) {
        $categoryMapper = new \Townspot\Category\Mapper($this->getServiceLocator());
        $data = array();
        foreach($cats as $c) {
            if($c instanceof \Townspot\Category\Entity) {
                $categories = $c->getCategoryTree();
                foreach($categories as $k => $t) {
                    switch($k) {
                        case 0:
                            if(empty($data[$t[0]])) {
                                $data[$t[0]] = array(
                                    'id' => $t[0],
                                    'name' => $t[1],
                                    'children' => array()
                                );
                            }
                            break;
                        case 1;
                            if(empty($data[$categories[0][0]]['children'][$t[0]])) {
                                $data[$categories[0][0]]['children'][$t[0]] = array(
                                    'id' => $t[0],
                                    'name' => $t[1],
                                    'children' => array()
                                );
                            }
                            break;
                        case 2;
                            if(empty($data[$categories[0][0]]['children'][$categories[1][0]][$t[0]])) {
                                $data[$categories[0][0]]['children'][$categories[1][0]][$t[0]] = array(
                                    'id' => $t[0],
                                    'name' => $t[1],
                                    'children' => false
                                );
                            }
                            break;
                    }

                }
            }
        }
        return $data;
    }
	
}
