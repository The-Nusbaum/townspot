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
                'media' => array()
            );

            $total = 0;
            foreach($series as $i => $s) {
                if($total == $limit) break;
                if($i < $offset) continue;
                $data['series'][] = $s->toArray();

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
} 