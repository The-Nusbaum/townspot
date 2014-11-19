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

class ModelController extends \Townspot\Controller\BaseRestfulController
{
    public function setEventManager(EventManagerInterface $eventManager)
    {
        parent::setEventManager($eventManager);

        $controller = $this;

        $eventManager->attach('dispatch', function ($e) use ($controller) {


            $model = ucFirst($controller->params()->fromRoute('model'));
            $method = $controller->params()->fromRoute('method');
            $id = $controller->params()->fromRoute('id');
            $this->setModel($model);
            $mapperClass = "\\Townspot\\".$model."\\Mapper";
            $controllerClass = "\\Api\\Controller\\{$this->getModel()}Controller";

            if(class_exists($controllerClass)) {
                if(method_exists(new $controllerClass, $method.'Action')) {
                    $response = $this->forward()->dispatch($controllerClass,array('action'=>$method,'id' => $id))->getVariables();
                } else $response = $this->forward()->dispatch($controllerClass)->getVariables();
                die(json_encode($response));
            }

            $this->setMapper(new $mapperClass($this->getServiceLocator()));
            $entityClass = "\\Townspot\\".$model."\\Entity";
            $this->setEntity(new $entityClass);
            $this->setResponse(new \Townspot\Rest\Response());
        }, 100);

    }
} 