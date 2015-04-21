<?php

namespace Api\Controller;

use Zend\EventManager\EventManagerInterface;
use Zend\View\Model\JsonModel;

class MediaCommentController extends \Townspot\Controller\BaseRestfulController
{
    public function setEventManager(EventManagerInterface $eventManager)
    {
        parent::setEventManager($eventManager);

        $controller = $this;

        $eventManager->attach('dispatch', function ($e) use ($controller) {
            $this->setModel('MediaComment');
            $this->setMapper(new \Townspot\MediaComment\Mapper($this->getServiceLocator()));
            $this->setEntity(new \Townspot\MediaComment\Entity());
            $this->setResponse(new \Townspot\Rest\Response());
        }, 100);

    }

    public function getformediaAction() {
        $id = $this->params()->fromRoute('id');
        $mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
        $media = $mediaMapper->find($id);
        $comments = $this->getMapper()->findBy(
            array(
                '_target' => $media
            )
        );

        $data = array();
        foreach($comments as $comment) {
            $data[] = $comment->toArray();
        }

        $this->getResponse()
            ->setCode(200)
            ->setSuccess(true)
            ->setData($data)
            ->setCount(count($data));

        return new JsonModel($this->getResponse()->build());
    }
}