<?php

namespace Api\Controller;

use Zend\EventManager\EventManagerInterface;
use Zend\View\Model\JsonModel;

class TrackingController extends \Townspot\Controller\BaseRestfulController
{
    public function setEventManager(EventManagerInterface $eventManager)
    {
        parent::setEventManager($eventManager);

        $controller = $this;

        $eventManager->attach('dispatch', function ($e) use ($controller) {
            $this->setModel('User');
            $this->setMapper(new \Townspot\User\Mapper($this->getServiceLocator()));
            $this->setEntity(new \Townspot\User\Entity());
            $this->setResponse(new \Townspot\Rest\Response());
        }, 100);
    }

    public function recordClickAction() {
        $type = $this->params()->fromRoute('type');
        $user = $this->params()->fromRoute('user');
        $value = $this->params()->fromRoute('value');


        $trackingMapper = new \Townspot\Tracking\Mapper($this->getServiceLocator());
        $tracking = new \Townspot\Tracking\Entity();

        $tracking->setType($type)
                 ->setUser($user)
                 ->setValue($value);

        $trackingMapper->setEntity($tracking)->save();

        $this->getResponse()
                ->setCode(200)
                ->setSuccess(true)
                ->setData(compact("type","user","value"))
                ->setCount(0);

        return new JsonModel($this->getResponse()->build());
    }
} 