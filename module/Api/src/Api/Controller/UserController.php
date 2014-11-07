<?php

namespace Api\Controller;

use Zend\EventManager\EventManagerInterface;
use Zend\View\Model\JsonModel;

class UserController extends \Townspot\Controller\BaseRestfulController
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

    public function checkUsernameAction() {
        $username = $this->params()->fromRoute('id');
        $user = $this->getMapper()->findByUsername($username);
        $this->getResponse()
            ->setCode(200)
            ->setSuccess(true)
            ->setData($username);
        if($user) $this->getResponse()
            ->setSuccess(false)
            ->setMessage('A user by that username has already been registered');
        return new JsonModel($this->getResponse()->build());
    }

    public function checkEmailAction() {
        $email = $this->params()->fromRoute('id');
        $user = $this->getMapper()->findByEmail($email);
        $this->getResponse()
            ->setCode(200)
            ->setSuccess(true)
            ->setData($email);
        if($user) $this->getResponse()
            ->setSuccess(false)
            ->setMessage('A user by that email address has already been registered');
        return new JsonModel($this->getResponse()->build());
    }
} 