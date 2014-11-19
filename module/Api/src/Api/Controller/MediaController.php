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
        $limit = 10;
        $offset = ($page - 1) * $limit;
        if (empty($id)) {
            $this->getResponse()->setCode(404)
                ->setSuccess(false)
                ->setMessage('No user id provided');
        } else {
            $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
            $user = $userMapper->find($id);

            $media = $this->getMapper()
                ->findByUser($user);

            $data = array(
                'media' => array()
            );

            $total = 0;
            foreach($media as $i => $m) {
                if($total == $limit) break;
                if($i < $offset) continue;
                if($m->getOnMediaServer()
                    && $m->getApproved()
                    && count($m->getEpisode()) == 0) {
                    $data['media'][] = $m->toArray();
                }
                $total++;
            }

            $data['totalCount'] = count($data['media']);
            $data['pages'] = ceil(count($data['media'])/$limit);

            $this->getResponse()
                ->setCode(200)
                ->setSuccess(true)
                ->setData($data)
                ->setCount(count($data['media']));
        }
        return new JsonModel($this->getResponse()->build());
    }
} 