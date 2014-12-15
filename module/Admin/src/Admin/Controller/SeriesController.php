<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SeriesController extends AbstractActionController
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
				
		return new ViewModel();
    }
	
    public function addAction()
    {
		$this->isAuthenticated();
	    $request = $this->getRequest();

		$userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
		$seriesMapper = new \Townspot\Series\Mapper($this->getServiceLocator());
		$_users = $userMapper->findAll();
		$users = array();
		foreach ($_users as $user) {
			if (($user->hasRole('Administrator'))||($user->hasRole('Artist'))) {
				$users[] = $user;
			}
		}
        $form = new \Admin\Forms\Series('series', $users);
        if($this->getRequest()->isPost()) {
			$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
			$episodeMapper = new \Townspot\SeriesEpisode\Mapper($this->getServiceLocator());
	        $data = $request->getPost();
			$user = $userMapper->find($data['user_id']);
			$series = new \Townspot\Series\Entity();
			$series->setUser($user)
				   ->setName($data['seriesname']);
			$seriesMapper->setEntity($series)->save();
			$episodes = explode(',',$data['selected_media']);
			$priority = 0;
			foreach ($episodes as $episode) {
				$priority++;
				$media = $mediaMapper->find($episode);
				$episode = new \Townspot\SeriesEpisode\Entity();
				$episode->setMedia($media)
					    ->setSeries($series)
						->setEpisodeNumber($priority);
				$episodeMapper->setEntity($episode)->save();
			}
            return $this->redirect()->toRoute('admin-series');
		}
		
		return new ViewModel( 
			array(
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
	    $request = $this->getRequest();
        $id = $this->params()->fromRoute('id');

		$userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
		$seriesMapper = new \Townspot\Series\Mapper($this->getServiceLocator());
		$_users = $userMapper->findAll();
		$users = array();
		foreach ($_users as $user) {
			if (($user->hasRole('Administrator'))||($user->hasRole('Artist'))) {
				$users[] = $user;
			}
		}
		$series = $seriesMapper->find($id);
		$selectedEpisodes 	= array();
		$displayedEpisodes 	= array();
		
		if ($series->getEpisodes()) {
			foreach ($series->getEpisodes() as $episode) {
				$selectedEpisodes[] = $episode->getMedia()->getId();
				$displayedEpisodes[] = $episode->getMedia();
			}
		}
		
        $form = new \Admin\Forms\Series('series', $users);
		$formData = array(
			'series_id'			=> $series->getId(),
			'seriesname'		=> $series->getName(),
			'selected_media'	=> implode(',',$selectedEpisodes)
		);
		
		$form->remove('user_id');
		$form->setData($formData);

		$availableEpisodes 	= $series->getUser()->getMedia();
		
        if($this->getRequest()->isPost()) {
			$mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
			$episodeMapper = new \Townspot\SeriesEpisode\Mapper($this->getServiceLocator());
			if ($series->getEpisodes()) {
				foreach ($series->getEpisodes() as $episode) {
					$episodeMapper->setEntity($episode)->delete();
				}
			}
	        $data = $request->getPost();
			$series->setName($data['seriesname']);
			$seriesMapper->setEntity($series)->save();
			$episodes = explode(',',$data['selected_media']);
			$priority = 0;
			foreach ($episodes as $episode) {
				$priority++;
				$media = $mediaMapper->find($episode);
				$episode = new \Townspot\SeriesEpisode\Entity();
				$episode->setMedia($media)
					    ->setSeries($series)
						->setEpisodeNumber($priority);
				$episodeMapper->setEntity($episode)->save();
			}
            return $this->redirect()->toRoute('admin-series');
		}
		return new ViewModel( 
			array(
				'form'				=> $form,
				'displayedEpisodes'	=> $displayedEpisodes,
				'availableEpisodes'	=> $availableEpisodes,
			)
		);
    }
	
    public function showAction()
    {
		$this->isAuthenticated();
		$id = $this->params()->fromRoute('id');
		$referrer = $this->params()->fromQuery('referrer');
		$seriesMapper = new \Townspot\Series\Mapper($this->getServiceLocator());
		$series 	= $seriesMapper->find($id);
		return new ViewModel( 
			array(
				'series'			=> $series,
				'referrer'	=> $referrer,
			)
		);
	}
	
}
