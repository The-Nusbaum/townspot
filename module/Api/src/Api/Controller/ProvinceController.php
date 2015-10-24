<?php

namespace Api\Controller;

use Zend\EventManager\EventManagerInterface;
use Zend\View\Model\JsonModel;

class ProvinceController extends \Townspot\Controller\BaseRestfulController
{
    public function setEventManager(EventManagerInterface $eventManager)
    {
        parent::setEventManager($eventManager);

        $controller = $this;

        $eventManager->attach('dispatch', function ($e) use ($controller) {
            $this->setModel('Province');
            $this->setMapper(new \Townspot\Province\Mapper($this->getServiceLocator()));
            $this->setEntity(new \Townspot\Province\Entity());
            $this->setResponse(new \Townspot\Rest\Response());
        }, 100);

    }

    public function getListAction() {
        $id = $this->params()->fromRoute('id');
        if($id){
            $provinces = array();
            $countryMapper = new \Townspot\Country\Mapper($this->getServiceLocator());
            $country = $countryMapper->find($id);
            foreach($country->getProvinces() as $province) {
                $provinces[] = array(
                    'id'        => $province->getId(),
                    'name'      => $province->getName()
                );
            }
            $this->getResponse()->setData($provinces);
        } else {
            $this->getResponse()
                ->setCode(404)
                ->setMessage('No Country Id Provided');
        }

        return new JsonModel($this->getResponse()->build());
    }
}