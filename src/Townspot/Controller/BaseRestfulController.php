<?php
/**
 * Created by IntelliJ IDEA.
 * User: ian
 * Date: 10/4/14
 * Time: 7:58 PM
 */

namespace Townspot\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Zend\EventManager\EventManagerInterface;
use Zend\Filter\Inflector;


class BaseRestfulController extends AbstractRestfulController
{
    protected $_model;
    protected $_mapper;
    protected $_entity;
    protected $_response;

    public function setEventManager(EventManagerInterface $eventManager)
    {
        header('Content-type: application/json');
        parent::setEventManager($eventManager);
    }
    public function getListAction()
    {   // Action used for GET requests without resource Id
        $this->getResponse()->setSuccess(false)
            ->setData(null)
            ->setMessage('shit be broke')
            ->setCode(501);
        return new JsonModel($this->getResponse()->build());
        $criteria = array();
        $query = $this->getRequest()->getQuery()['query'];
        $query = explode(',',$query);

        $entities = $this->getMapper()->findBy(array('user_id'=>242));
        $data = array();
        foreach($entities as $e) {
            try {
                $data[] = $e->toArray();
            } catch (Exception $e) {
                //do nothing
            }
        }
        var_dump($data);
        return new JsonModel($this->getResponse()->build());
    }

    public function getAction()
    {   // Action used for GET requests with resource Id
        $id = $this->params()->fromRoute('id');
        $this->setEntity($this->getMapper()->findOneById($id));
        if($this->getEntity()) {
            $this->getResponse()
                ->setCount(1)
                ->setData($this->getEntity()->toArray());
        } else {
            $this->getResponse()->setCode(404)
                ->setSuccess(false)
                ->setMessage($this->getModel()." record was not found");
        }
        return new JsonModel($this->getResponse()->build());
    }

    public function createAction($data)
    {   // Action used for POST requests
        $this->getMapper()->setEntity($this->getEntity());
        foreach($this->params()->fromPost() as $field => $value) {
            $inflector = new Inflector(':field');
            $inflector->setRules(array(
                ':field' => array('Word\UnderscoreToCamelCase')
            ));
            if(preg_match('/_id$/',$field)) {
                $field = str_replace('_id', '', $field);
                if($field == 'admin') $dataSource = 'User';
                else {
                    $dataSource = $inflector->filter(array('field' => $field));
                }
                $mapperClass = "\\Townspot\\$dataSource\\Mapper";
                $mapper = new $mapperClass($this->getServiceLocator());
                $value = $mapper->findOneById($value);
            }

            $method = "set".$inflector->filter(array('field' => $field));
            if(method_exists($this->getEntity(),$method)) {
                $this->getEntity()->{$method}($value);
            }
        }
        $this->getMapper()->save();
        $this->getResponse()
            ->setCount(1)
            ->setData($this->getEntity()->toArray());

        return new JsonModel($this->getResponse()->build());
    }

    public function updateAction()
    {   // Action used for PUT requests
        $id = $this->params()->fromRoute('id');
        $data = $this->params()->fromPost('data');
        $this->setEntity($this->getMapper()->findOneById($id));

        if($this->getEntity()) {
            $this->getMapper()->setEntity($this->getEntity());
            foreach($data as $field => $value) {
                $inflector = new Inflector(':field');
                $inflector->setRules(array(
                    ':field' => array('Word\UnderscoreToCamelCase')
                ));
                if(preg_match('/_id$/',$field)) {
                    $field = str_replace('_id', '', $field);
                    if($field == 'admin') $dataSource = 'User';
                    else {
                        $dataSource = $inflector->filter(array('field' => $field));
                    }
                    $mapperClass = "\\Townspot\\$dataSource\\Mapper";
                    $mapper = new $mapperClass($this->getServiceLocator());
                    $value = $mapper->findOneById($value);
                }

                $method = "set".$inflector->filter(array('field' => $field));
                if(method_exists($this->getEntity(),$method)) {
                    $this->getEntity()->{$method}($value);
                }
            }
            $this->getMapper()->save();
            $this->getResponse()
                ->setCount(1)
                ->setData($this->getMapper()->getEntity()->toArray());
        } else {
            $this->getResponse()->setCode(404)
                ->setSuccess(false)
                ->setMessage($this->getModel()." record was not found");
        }


        return new JsonModel($this->getResponse()->build());
    }

    public function deleteAction()
    {   // Action used for DELETE requests
        $id = $this->params()->fromRoute('id');
        $this->setEntity($this->getMapper()->find($id));

        if($this->getEntity()) {
            $this->getMapper()->setEntity($this->getEntity());
            $this->getMapper()->delete();
            $this->getResponse()
                ->setMessage($this->getModel()." record was deleted");
        } else {
            $this->getResponse()->setCode(404)
                ->setSuccess(false)
                ->setMessage($this->getModel()." record was not found");
        }


        return new JsonModel($this->getResponse()->build());
    }

    public function deleteListAction($id) {
        return $this->delete($id);
    }

    function parse_raw_http_request($input)
    {
        foreach($input as $i) {
            $input = $i;
            break;
        }
        // grab multipart boundary from content type header
        preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);
        $boundary = $matches[1];

        $input = preg_replace("/--$boundary(?:--)?/",'',$input);
        $input = preg_replace("/Content-Disposition: form-data; name=/",'',$input);
        $input = preg_replace("/\r\n/",'',$input);
        $input = ltrim($input,'"');
        $input = explode('"',$input);
        foreach($input as $k => $v) {
            if($k % 2 == 0) {
                $data[$v] = $input[$k+1];
            } else continue;
        }
        return $data;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->_entity;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity)
    {
        $this->_entity = $entity;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMapper()
    {
        return $this->_mapper;
    }

    /**
     * @param mixed $mapper
     */
    public function setMapper($mapper)
    {
        $this->_mapper = $mapper;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * @param mixed $model
     */
    public function setModel($model)
    {
        $this->_model = $model;
        return $this;
    }

    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * @param mixed $model
     */
    public function setResponse($response)
    {
        $this->_response = $response;
        return $this;
    }
} 