<?php

namespace Api\Controller;

use Zend\EventManager\EventManagerInterface;
use Zend\View\Model\JsonModel;

class ArtistCommentController extends \Townspot\Controller\BaseRestfulController
{
    public function setEventManager(EventManagerInterface $eventManager)
    {
        parent::setEventManager($eventManager);

        $controller = $this;

        $eventManager->attach('dispatch', function ($e) use ($controller) {
            $this->setModel('ArtistComment');
            $this->setMapper(new \Townspot\ArtistComment\Mapper($this->getServiceLocator()));
            $this->setEntity(new \Townspot\ArtistComment\Entity());
            $this->setResponse(new \Townspot\Rest\Response());
        }, 100);

    }

    public function getforartistAction() {
        $id = $this->params()->fromRoute('id');
        $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
        $user = $userMapper->find($id);
        $comments = $this->getMapper()->findBy(
            array(
                '_target' => $user
            )
        );

        $data = array();
        foreach($comments as $comment) {
            $_c = $comment->toArray();
            $commenter = $comment->getUser();
            $_c['user_id'] = $commenter->getId();
            $_c['username'] = $commenter->getDisplayName();
            $data[] = $_c;
        }

        $this->getResponse()
            ->setCode(200)
            ->setSuccess(true)
            ->setData($data)
            ->setCount(count($data));

        return new JsonModel($this->getResponse()->build());
    }
}