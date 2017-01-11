<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MediaController extends AbstractActionController
{
    protected $_api_info = array(
        'applicationId' => 'Townspot',
        'clientId' => "872367745273-sbsiuc81kh9o70ok3macc15d2ebpl440.apps.googleusercontent.com",
        'developerId' => "AIzaSyCa1RYJsf-C94cTQo34GC59DkiijUq_54s",
    );

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

        $config = $this->getServiceLocator()->get('config');

        if($this->getRequest()->isPost()){
            if($data->get('youtube_url') && !$data->get('review_ok')) {
                /*
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
                */

                $client = new \Google_Client();
                $client->setApplicationName($this->_api_info['applicationId']);
                $client->setDeveloperKey($this->_api_info['developerId']);
                $yt = new \Google_Service_YouTube($client);
                preg_match('/\?v=([^&]*)/',$data['youtube_url'],$matches);
                $id = $matches[1];
                $ytVideo = $yt->videos->listVideos("snippet,contentDetails,statistics",
                    array('id' => $id))->getItems()[0];
                //$ytVideo = $yt->getVideoEntry($id);


                preg_match('/v=([^&]*)/i',$data->get('youtube_url'),$idMatches);
                if(!empty($idMatches[1])) {
                    $id = $idMatches[1];
                } else {
                    //no id
                }

                $duration = $ytVideo->getContentDetails()->getDuration();
                preg_match('/([0-9]*)H/',$duration,$hours);
                preg_match('/([0-9]*)M/',$duration,$minutes);
                preg_match('/([0-9]*)S/',$duration,$seconds);

                $durationInSecs = 0;
                if(!empty($hours[1])) $durationInSecs += $hours[1] * 60 * 60;
                if(!empty($minutes[1])) $durationInSecs += $minutes[1] * 60;
                if(!empty($seconds[1])) $durationInSecs += $seconds[1];

                $duration = $durationInSecs;

                $data->set('title',$ytVideo->getSnippet()->getTitle())
                    ->set('description',$ytVideo->getSnippet()->getDescription())
                    ->set('duration',$duration)
                    ->set('on_media_server',1)
                    ->set('preview_url',$ytVideo->getSnippet()->getThumbnails()->getHigh()->getUrl())
                    ->set('previewImage',$ytVideo->getSnippet()->getThumbnails()->getHigh()->getUrl())
                    ->set('source','youtube')
                    ->set('video_url',$data->get('youtube_url'))
                    ->set('url',$data->get('youtube_url'));

            } elseif($data->get('vimeo_url') && !$data->get('review_ok')) {
                $url = $data->get('vimeo_url');
                $id = str_replace('https://vimeo.com/','',$data->get('vimeo_url'));
                $vimeo = new \Vimeo\Vimeo('ac278d2d73248632ac83bf9fc43900876b9c12e0', '68c3d6ee56c6a66a2c4e6f05c06f0199f84b94c3');
                $token = '45dd4e70cfd1a1b307c683c1b5deff2a';
                $vimeo->setToken($token);
                $response = $vimeo->request("/videos/$id");

                $data->set('title', $response['body']['name'])
                    ->set('description', $response['body']['description'])
                    ->set('duration', $response['body']['duration'])
                    ->set('on_media_server', 1)
                    ->set('preview_url', $response['body']['pictures']['sizes'][5]['link'])
                    ->set('previewImage', $response['body']['pictures']['sizes'][3]['link'])
                    ->set('source', 'vimeo')
                    ->set('video_url', $url)
                    ->set('url', $url);
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
                $selCat = $data->get('selCat');
		if(!empty($selCat)) {
                    foreach ($data->get('selCat') as $vc) {
                        $cat = $categoryMapper->find($vc);
                        $mediaEntity->addCategory($cat);
                    }
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
                'country'         => $country->getName(),
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
        //$country = $countryMapper->find(99);
		$country = array();

        $provinceMapper = new \Townspot\Province\Mapper($this->getServiceLocator());
        //$provinces = $provinceMapper->findByCountry($country);
        $provinces = array();

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
        $vimeoForm = new \Application\Forms\Video\Upload\VimeoForm();
        $manualForm = new \Application\Forms\Video\Upload\ManualForm('manualForm');
        $footerForm = new \Application\Forms\Video\Upload\FooterForm('footerForm');
        $videoMediaForm = new \Application\Forms\Video\Upload\VideoMediaForm('videoMediaForm');


        $this->getServiceLocator()
            ->get('viewhelpermanager')
            ->get('HeadScript')
                //->appendFile('/js/townspot.js')
				->appendFile('/js/adminupload.js');

		return new ViewModel( 
			array(
				'headForm'			=> $headForm,
				'ytForm'			=> $ytForm,
				'vimeoForm'			=> $vimeoForm,
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
                $provinces = $provinceMapper->findByCountry($media->getCountry());
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
                    ->get('HeadScript')
                        //->appendFile('/js/townspot.js')
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

    private function _youtube_thumbs($media) {
        $id = str_replace('https://www.youtube.com/watch?v=','',$media->getUrl());
        $content = file_get_contents("http://youtube.com/get_video_info?video_id=".$id);
        parse_str($content, $ytarr);
        $fullstr = $ytarr["url_encoded_fmt_stream_map"];
        $estr = explode(',',$fullstr);
        foreach($estr as $e) {
            parse_str($e,$atarget);
            if(preg_match('/video\/mp4/',$atarget['type'])) break;
        }
        $str = file_get_contents($atarget['url']);
        file_put_contents(APPLICATION_PATH . "/public/thumb.mp4", $str);
    }

    private function _vimeo_thumbs($media) {
        $id = str_replace('https://vimeo.com/','',$media->getUrl());
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_ENCODING , "gzip");

        curl_setopt($ch, CURLOPT_URL, "https://vimeo.com/$id");

        curl_exec($ch);

        $headers = array(
            'GET /|id|?action=load_download_config HTTP/1.1',
            'Host: vimeo.com',
            'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:49.0) Gecko/20100101 Firefox/49.0',
            'Accept: */*',
            'Accept-Language: en-US,en;q=0.5',
            'Accept-Encoding: gzip, deflate, br',
            'X-Requested-With: XMLHttpRequest',
            'Origin: https://vimeo.com',
            'Referer: https://vimeo.com/|id|',
            'Connection: keep-alive'
        );

        $headers[0] = str_replace('|id|',$id,$headers[0]);
        $headers[8] = str_replace('|id|',$id,$headers[8]);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_URL, "https://vimeo.com/$id?action=load_download_config");

        $response = json_decode(curl_exec($ch));

        $fh = fopen(APPLICATION_PATH . "/public/thumb.mp4",'w');

        curl_setopt($ch, CURLOPT_URL, $response->files['0']->download_url);
        curl_setopt($ch, CURLOPT_FILE, $fh);

        $response = curl_exec($ch);
    }

    private function _dailymotion_thumbs($media) {
        set_time_limit(300);
        $url = $media->getUrl();
        $ch = curl_init();
//        $url = "http://www.dailymotion.com/video/x529eyv_barack-obama-in-athens-democracy-can-be-complicated-video_news";

        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Host: www.dailymotion.com',
            'Connection: keep-alive',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.99 Safari/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            // 'Accept-Encoding: gzip, deflate, sdch',
            'Accept-Language: en-US,en;q=0.8',
        ));

        $resp = curl_exec($ch);
        preg_match_all("/var config = (.*);/",$resp,$matches);
        $data = json_decode($matches[1][0]);
        $q = "480";
        $q = (array) $data->metadata->qualities->$q;

        $url = $q[1]->url;

        $fh = fopen(APPLICATION_PATH . "/public/thumb.mp4","w");

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FILE, $fh);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, -1); # optional: -1 = unlimited, 3600 = 1 hour
        curl_setopt($ch, CURLOPT_VERBOSE, true); # Set to true to see all the innards

        # Only if you need to bypass SSL certificate validation
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        # Assign a callback function to the CURL Write-Function
        // curl_setopt($ch, CURLOPT_WRITEFUNCTION, 'curlWriteFile');
        $resp = curl_exec($ch);
    }

    private function _internal_thumbs($media) {
        set_time_limit(300);
        $url = $media->getUrl();
        $ch = curl_init();

//        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
//        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
//        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        $fh = fopen(APPLICATION_PATH . "/public/thumb.mp4","w");
        
	curl_setopt($ch, CURLOPT_FILE, $fh);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, -1); # optional: -1 = unlimited, 3600 = 1 hour
        curl_setopt($ch, CURLOPT_VERBOSE, true); # Set to true to see all the innards

        # Only if you need to bypass SSL certificate validation
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        # Assign a callback function to the CURL Write-Function
        // curl_setopt($ch, CURLOPT_WRITEFUNCTION, 'curlWriteFile');
        $resp = curl_exec($ch);
    }

    public function thumbsAction() {
        $this->layout('admin/empty');
        $id = $this->params()->fromRoute('id');
        $mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
        $media = $mediaMapper->find($id);
        switch($media->getSource()){
            case 'youtube':
                $this->_youtube_thumbs($media);
                break;
            case 'vimeo':
                $this->_vimeo_thumbs($media);
                break;
     	    case 'dailymotion':
                $this->_dailymotion_thumbs($media);
                break;
            default:
                $this->_internal_thumbs($media);
        }

        return new ViewModel(
            array(
                'id'		=> $media->getId(),
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
