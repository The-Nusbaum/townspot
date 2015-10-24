<?php

namespace Api\Controller;

use Zend\EventManager\EventManagerInterface;
use Zend\View\Model\JsonModel;

class CityController extends \Townspot\Controller\BaseRestfulController
{
    public function setEventManager(EventManagerInterface $eventManager)
    {
        parent::setEventManager($eventManager);

        $controller = $this;

        $eventManager->attach('dispatch', function ($e) use ($controller) {
            $this->setModel('City');
            $this->setMapper(new \Townspot\City\Mapper($this->getServiceLocator()));
            $this->setEntity(new \Townspot\City\Entity());
            $this->setResponse(new \Townspot\Rest\Response());
        }, 100);

    }

    public function getListAction() {
        $id = $this->params()->fromRoute('id');
        if($id){
            $cities = array();
            $provinceMapper = new \Townspot\Province\Mapper($this->getServiceLocator());
            $province = $provinceMapper->find($id);
            foreach($province->getCities() as $city) {
                $cities[] = array(
                    'id'    => $city->getId(),
                    'name'  => htmlentities($city->getName(),ENT_SUBSTITUTE)
                );
            }
            $this->getResponse()->setData($cities);
        } else {
            $this->getResponse()
                ->setCode(404)
                ->setMessage('No Province Id Provided');
        }

        return new JsonModel($this->getResponse()->build());
    }
}