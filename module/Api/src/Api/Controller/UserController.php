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

    public function removeFavoriteAction() {
        $auth =  new \Zend\Authentication\AuthenticationService();
        $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
        $user = $userMapper->find($auth->getIdentity());

        if(empty($user)) {
            $this->getResponse()
                ->setSuccess(false)
                ->setMessage('You must be logged in to perform this action');
            return new JsonModel($this->getResponse()->build());
        }

        $id = $this->params()->fromRoute('id');

            $user->removeFavorite($id);
            $userMapper->setEntity($user)->save();
            $this->getResponse()
                ->setCode(200)
                ->setSuccess(true);

        return new JsonModel($this->getResponse()->build());
    }

    public function addFavoriteAction() {
        $auth =  new \Zend\Authentication\AuthenticationService();
        $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
        $user = $userMapper->find($auth->getIdentity());

        if(empty($user)) {
            $this->getResponse()
                ->setSuccess(false)
                ->setMessage('You must be logged in to perform this action');
            return new JsonModel($this->getResponse()->build());
        }

        $id = $this->params()->fromRoute('id');

        $mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
        $media = $mediaMapper->find($id);

        $user->addFavorite($media);
        $userMapper->setEntity($user)->save();
        $this->getResponse()
            ->setCode(200)
            ->setSuccess(true);

        return new JsonModel($this->getResponse()->build());
    }
} 