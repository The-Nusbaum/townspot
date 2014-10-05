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

class MediaController extends AbstractRestfulController
{
    public function getList()
    {   // Action used for GET requests without resource Id
        $VideoMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
        return new JsonModel(
            array('data' =>
                $MediaMapper->find()
            )
        );
    }

    public function get($id)
    {   // Action used for GET requests with resource Id
        return new JsonModel(array("data" => array('id'=> 2, 'name' => 'Coda', 'band' => 'Led Zeppelin')));
    }

    public function create($data)
    {   // Action used for POST requests
        return new JsonModel(array('data' => array('id'=> 3, 'name' => 'New Album', 'band' => 'New Band')));
    }

    public function update($id, $data)
    {   // Action used for PUT requests
        return new JsonModel(array('data' => array('id'=> 3, 'name' => 'Updated Album', 'band' => 'Updated Band')));
    }

    public function delete($id)
    {   // Action used for DELETE requests
        return new JsonModel(array('data' => 'album id 3 deleted'));
    }
} 