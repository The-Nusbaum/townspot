<?php

namespace Api\Controller;

use Zend\EventManager\EventManagerInterface;
use Zend\View\Model\JsonModel;

class PlaylistController extends \Townspot\Controller\BaseRestfulController
{
    public function setEventManager(EventManagerInterface $eventManager)
    {
        parent::setEventManager($eventManager);

        $controller = $this;

        $eventManager->attach('dispatch', function ($e) use ($controller) {
            $this->setModel('Playlist');
            $this->setMapper(new \Townspot\Playlist\Mapper($this->getServiceLocator()));
            $this->setEntity(new \Townspot\Playlist\Entity());
            $this->setResponse(new \Townspot\Rest\Response());
        }, 100);
    }

    protected function _playlist(\Townspot\Playlist\Entity $raw) {
        $media = array();
        foreach($raw->getMedia() as $m) {
            $media[] = $m->toArray();
        }
        $data[] = array(
            'id' => $raw->getId(),
            'name' => $raw->getName(),
            'desc' => $raw->getDescription(),
            'media' => $media
        );

        return $data;
    }

    public function getAction() {
        $id = $this->params()->fromRoute('id');

        $playListMapper = new \Townspot\Playlist\Mapper($this->getServiceLocator());
        $playlist = $playListMapper->find($id);

        $data = $this->_playlist($playlist);

        $this->getResponse()
            ->setCode(200)
            ->setSuccess(true)
            ->setData($data)
            ->setCount(count($data));

        return new JsonModel($this->getResponse()->build());
    }

    public function userAction() {
        $id = $this->params()->fromRoute('id');

        $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
        $user = $userMapper->find($id);

        $playlists = $user->getPlaylists();

        $data = array();

        foreach($playlists as $p) {
            $data[] = $this->_playlist($p);
        }

        $this->getResponse()
            ->setCode(200)
            ->setSuccess(true)
            ->setData($data)
            ->setCount(count($data));

        return new JsonModel($this->getResponse()->build());
    }

    public function listAction() {
        $id = $this->params()->fromRoute('id');

        $playListMapper = new \Townspot\Playlist\Mapper($this->getServiceLocator());
        $playlists = $playListMapper->findAll();

        $data = array();

        foreach($playlists as $p) {
            $data[] = $this->_playlist($p);
        }

        $this->getResponse()
            ->setCode(200)
            ->setSuccess(true)
            ->setData($data)
            ->setCount(count($data));

        return new JsonModel($this->getResponse()->build());
    }

    public function addAction() {
        $pid = $this->params()->fromRoute('id');
        $mid = $this->params()->fromQuery('mid');

        $playListMapper = new \Townspot\Playlist\Mapper($this->getServiceLocator());
        $playlist = $playListMapper->find($pid);

        $mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
        $media = $mediaMapper->find($mid);

        $add = true;
        foreach($playlist->getMedia as $m) if($m->getId() == $mid) $add = false;
        if ($add) $playlist->addMedia($media);

        $data = $this->_playlist($playlist);

        $this->getResponse()
            ->setCode(200)
            ->setSuccess(true)
            ->setData($data)
            ->setCount(count($data));

        return new JsonModel($this->getResponse()->build());
    }

    public function removeAction() {
        $pid = $this->params()->fromRoute('id');
        $mid = $this->params()->fromQuery('mid');

        $playListMapper = new \Townspot\Playlist\Mapper($this->getServiceLocator());
        $playlist = $playListMapper->find($pid);

        $mediaMapper = new \Townspot\Media\Mapper($this->getServiceLocator());
        $media = $mediaMapper->find($mid);

        $playlist->removeMedia($media);

        $data = $playlist->toArray();

        $this->getResponse()
            ->setCode(200)
            ->setSuccess(true)
            ->setData($data)
            ->setCount(count($data));

        return new JsonModel($this->getResponse()->build());
    }

    public function createAction() {
        $uid = $this->params()->fromPost('uid');
        $name = $this->params()->fromPost('name');
        $desc = $this->params()->fromPost('desc');

        $playListMapper = new \Townspot\Playlist\Mapper($this->getServiceLocator());
        $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());

        $success = true;
        $code = 200;

        $user = $userMapper->find($uid);
        if($user instanceof \Townspot\User\Entity) {
            $playlist = new \Townspot\Playlist\Entity();
            $playlist->setUser($user);
            $playlist->setName($name);
            $playlist->setDesc($desc);
        } else {
            $success = false;
            $data = "No such user";
            $code = 404;
        }

        $playListMapper->save($playlist);

        $data = $this->_playlist($playlist);

        $this->getResponse()
            ->setCode($code)
            ->setSuccess($success)
            ->setData($data)
            ->setCount(count($data));

        return new JsonModel($this->getResponse()->build());
    }

    public function updateAction() {
        $pid = $this->params()->fromRoute('id');
        $name = $this->params()->fromPost('name');
        $desc = $this->params()->fromPost('desc');

        $playListMapper = new \Townspot\Playlist\Mapper($this->getServiceLocator());

        $success = true;
        $code = 200;

        $playlist = $playListMapper->find($pid);
        $playlist->setName($name);
        $playlist->setDesc($desc);
        $playListMapper->save($playlist);

        $data = $this->_playlist($playlist);

        $this->getResponse()
            ->setCode($code)
            ->setSuccess($success)
            ->setData($data)
            ->setCount(count($data));

        return new JsonModel($this->getResponse()->build());
    }

    public function deleteAction() {
        $pid = $this->params()->fromRoute('id');

        $playListMapper = new \Townspot\Playlist\Mapper($this->getServiceLocator());
        $playlist = $playListMapper->find($pid);

        $playListMapper->setEntity($playlist)->delete();

        $data = null;

        $this->getResponse()
            ->setCode(200)
            ->setSuccess(true)
            ->setData($data)
            ->setCount(count($data));

        return new JsonModel($this->getResponse()->build());
    }
} 