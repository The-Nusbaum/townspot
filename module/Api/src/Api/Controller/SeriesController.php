<?php

namespace Api\Controller;

use Zend\EventManager\EventManagerInterface;
use Zend\View\Model\JsonModel;

class MediaController extends \Townspot\Controller\BaseRestfulController
{
    public function setEventManager(EventManagerInterface $eventManager)
    {
        parent::setEventManager($eventManager);

        $controller = $this;

        $eventManager->attach('dispatch', function ($e) use ($controller) {
            $this->setModel('Media');
            $this->setMapper(new \Townspot\Media\Mapper($this->getServiceLocator()));
            $this->setEntity(new \Townspot\Media\Entity());
            $this->setResponse(new \Townspot\Rest\Response());
        }, 100);

    }

    public function getAvailableSeriesMediaAction() {
        $id = $this->params()->fromRoute('id');
        $page = $this->params()->fromQuery('page');
        if(!$page) $page = 1;
        $limit = 2;
        $offset = ($page - 1) * $limit;
        if (empty($id)) {
            $this->getResponse()->setCode(404)
                ->setSuccess(false)
                ->setMessage('No user id provided');
        } else {
            $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
            $user = $userMapper->find($id);

            $seriesMapper = new \Townspot\Series\Mapper($this->getServiceLocator());
            $series = $seriesMapper->findByUserId($user->getId());

            $seriesMediaMapper = new \Townspot\SectionMedia\Mapper($this->getServiceLocator());

            $inSeriesList = array();
            foreach($series as $s) {
                $seriesMedia = $seriesMediaMapper->findBySeriesId($s->getId());
                foreach($seriesMedia as $sm){
                    $inSeriesList[] = $sm->getMediaId();
                }

            }

            $media = $this->getMapper()
                ->findByUser($user);

            $data = array(
                'totalCount' => count($media),
                'pages' => ceil(count($media)/$limit),
                'media' => array()
            );

            $total = 0;
            foreach($media as $i => $m) {
                if($total == $limit) break;
                if($i < $offset) continue;
                if($m->getOnMediaServer() && $m->getApproved()) {
                    $data['media'][] = $m->toArray();
                }
                $total++;
            }

            $this->getResponse()
                ->setCode(200)
                ->setSuccess(true)
                ->setData($data)
                ->setCount(count($data['media']));
        }
        return new JsonModel($this->getResponse()->build());
    }
} 