<?php
/**
 * Created by IntelliJ IDEA.
 * User: ian
 * Date: 10/4/14
 * Time: 4:23 AM
 */

namespace Api\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Zend\EventManager\EventManagerInterface;
use Zend\Filter\Inflector;

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
} 