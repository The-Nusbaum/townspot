<?php

namespace Api\Controller;

use Zend\EventManager\EventManagerInterface;
use Zend\View\Model\JsonModel;

class CategoryController extends \Townspot\Controller\BaseRestfulController
{
    public function setEventManager(EventManagerInterface $eventManager)
    {
        parent::setEventManager($eventManager);

        $controller = $this;

        $eventManager->attach('dispatch', function ($e) use ($controller) {
            $this->setModel('Category');
            $this->setMapper(new \Townspot\Category\Mapper($this->getServiceLocator()));
            $this->setEntity(new \Townspot\Category\Entity());
            $this->setResponse(new \Townspot\Rest\Response());
        }, 100);

    }

    protected function _getChildren($id) {
        return $this->getMapper()->findByParent($id);
    }

    protected function _prepCategory($Category) {
        $data = array(
            'name' => $Category->getName(),
            'id' => $Category->getId()
        );
        $children = $this->_getChildren($Category->getId());
        if(count($children)) {
            foreach($children as $c) {
                $data['children'][$c->getId()] = $this->_prepCategory($c);
            }

        } else {
            $data['children'] = false;
        }
        return $data;
    }

    public function getTieredCategoriesAction() {
        $topLevel = $this->_getChildren(0);

        $data = array();

        foreach($topLevel as $top) {
            $data[$top->getId()] = $this->_prepCategory($top);
        }
        $this->getResponse()
            ->setData($data)
            ->setCount(count($data));
        return new JsonModel($this->getResponse()->build());
    }
} 