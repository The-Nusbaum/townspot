<?php

namespace Api\Controller;

use Zend\EventManager\EventManagerInterface;
use Zend\View\Model\JsonModel;

class SeriesController extends \Townspot\Controller\BaseRestfulController
{
    public function setEventManager(EventManagerInterface $eventManager)
    {
        parent::setEventManager($eventManager);

        $controller = $this;

        $eventManager->attach('dispatch', function ($e) use ($controller) {
            $this->setModel('Series');
            $this->setMapper(new \Townspot\Series\Mapper($this->getServiceLocator()));
            $this->setEntity(new \Townspot\Series\Entity());
            $this->setResponse(new \Townspot\Rest\Response());
        }, 100);

    }

    public function getUserSeriesAction() {
        $id = $this->params()->fromRoute('id');
        $page = $this->params()->fromQuery('page');
        if(!$page) $page = 1;
        $limit = 5;
        $offset = ($page - 1) * $limit;
        if (empty($id)) {
            $this->getResponse()->setCode(404)
                ->setSuccess(false)
                ->setMessage('No user id provided');
        } else {
            $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
            $user = $userMapper->find($id);

            $seriesMapper = new \Townspot\Series\Mapper($this->getServiceLocator());
            $series = $seriesMapper->findByUser($user);

            $data = array(
                'totalCount' => count($series),
                'pages' => ceil(count($series)/$limit),
                'series' => array()
            );

            $episodeMapper = new \Townspot\SeriesEpisode\Mapper($this->getServiceLocator());

            $total = 0;
            foreach($series as $i => $s) {
                if($total == $limit) break;
                if($i < $offset) continue;
                $tmpData = $s->toArray();
                $episodes = $s->getEpisodes();
                $tmpData['episodes'] = array();
                foreach($episodes as $e){
                    $eData = $e->toArray();
                    $eData['title'] = $e->getMedia()->getTitle();
                    $tmpData['episodes'][] = $eData;
                }
                $data['series'][] = $tmpData;

                $total++;
            }

            $this->getResponse()
                ->setCode(200)
                ->setSuccess(true)
                ->setData($data)
                ->setCount(count($data['series']));
        }
        return new JsonModel($this->getResponse()->build());
    }

    public function saveAction() {
        $series_id = $this->params()->fromPost('series_id');
        $episodes = $this->params()->fromPost('episodes');

        $seriesMapper = new \Townspot\Series\Mapper($this->getServiceLocator());
        $episodeMapper = new \Townspot\SeriesEpisode\Mapper($this->getServiceLocator());
        $mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());

        $series = $seriesMapper->find($series_id);

        foreach($series->getEpisodes() as $key => $episode) {
            $episodeMapper->setEntity($episode)->delete();
        }

        foreach($episodes as $episodeData) {
            $media = $mediaMapper->find($episodeData['media_id']);
            $episode = new \Townspot\SeriesEpisode\Entity();
            $episode->setEpisodeNumber($episodeData['episode_number'])
                ->setMedia($media)
                ->setSeries($series);
            $episodeMapper->setEntity($episode)->save();
        }
    }

    public function deleteAction()
    {   // Action used for DELETE requests
        $id = $this->params()->fromRoute('id');
        $this->setEntity($this->getMapper()->find($id));

        if($this->getEntity()) {
            $episodeMapper = new \Townspot\SeriesEpisode\Mapper($this->getServiceLocator());
            foreach($this->getEntity()->getEpisodes() as $e) {
                $episodeMapper->setEntity($e)->delete();
            }
            $this->getMapper()->setEntity($this->getEntity());
            $this->getMapper()->delete();
            $this->getResponse()
                ->setMessage($this->getModel()." record was deleted");
        } else {
            $this->getResponse()->setCode(404)
                ->setSuccess(false)
                ->setMessage($this->getModel()." record was not found");
        }


        return new JsonModel($this->getResponse()->build());
    }
} 