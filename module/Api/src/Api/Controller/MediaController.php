<?php

namespace Api\Controller;

use Zend\EventManager\EventManagerInterface;
use Zend\View\Model\JsonModel;

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

    public function commentsAction(){
        $id = $this->params()->fromRoute('id');
        $media = $this->getMapper()->find($id);
        $comments = $media->getComments();


        $data = $comments;

        $this->getResponse()
            ->setCode(200)
            ->setSuccess(true)
            ->setData($data)
            ->setCount(count($data['media']));
    }

    public function slot1Action() {
        $id = $this->params()->fromRoute('id');
        $media = $this->getMapper()->slot1($id);

        $output[] = $media->toArray();

        $this->getResponse()
            ->setCode(200)
            ->setSuccess(true)
            ->setData($output)
            ->setCount(1);
        return new JsonModel($this->getResponse()->build());
    }


    public function slot2Action() {
        $media = $this->getMapper()->slot2();

        $output[] = $media->toArray();

        $this->getResponse()
            ->setCode(200)
            ->setSuccess(true)
            ->setData($output)
            ->setCount(1);
        return new JsonModel($this->getResponse()->build());
    }

    public function slot3Action() {
        $media = $this->getMapper()->slot3();

        $output[] = $media->toArray();

        $this->getResponse()
            ->setCode(200)
            ->setSuccess(true)
            ->setData($output)
            ->setCount(1);
        return new JsonModel($this->getResponse()->build());
    }

    public function getAvailableSeriesMediaAction() {
        $id = $this->params()->fromRoute('id');
        $page = $this->params()->fromQuery('page');
        if(!$page) $page = 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        if (empty($id)) {
            $this->getResponse()->setCode(404)
                ->setSuccess(false)
                ->setMessage('No user id provided');
        } else {
            $userMapper = new \Townspot\User\Mapper($this->getServiceLocator());
            $user = $userMapper->find($id);

            $media = $this->getMapper()
                ->findByUser($user);

            $data = array(
                'media' => array()
            );

            $total = 0;
            foreach($media as $i => $m) {
                if($total == $limit) break;
                if($i < $offset) continue;
                if($m->getOnMediaServer()
                    && $m->getApproved()
                    && count($m->getEpisode()) == 0) {
                    $data['media'][] = $m->toArray();
                }
                $total++;
            }

            $data['totalCount'] = count($data['media']);
            $data['pages'] = ceil(count($data['media'])/$limit);

            $this->getResponse()
                ->setCode(200)
                ->setSuccess(true)
                ->setData($data)
                ->setCount(count($data['media']));
        }
        return new JsonModel($this->getResponse()->build());
    }

    protected function _resolveCats($category) {
        $_cat = array();
        $_cat[] = $category->getName();
        if($category->getParent()) {
            $_cat = array_merge($_cat,$this->_resolveCats($category->getParent()));
        }

        return $_cat;
    }

    public function channelsurfAction() {
        $seriesMapper 	= new \Townspot\Series\Mapper($this->getServiceLocator());

        $state = $this->params()->fromPost('state');
        $city = $this->params()->fromPost('city');
        $category = $this->params()->fromPost('category');
        $limit = $this->params()->fromPost('limit');
        $notThese = $this->params()->fromPost('notThese');
        $sort = $this->params()->fromPost('sort');

        $matches = $this->getMapper()->getChannelsurfMedia($state,$city,$category,$limit,$notThese,$sort);
        $data = array();
        $data['media'] = array();
        foreach($matches as $match) {
            $media = $this->getMapper()->find($match['id']);
            $categories = array();
            $cats = $media->getCategories();
            foreach($cats as $c) {
                $categories[] = array(
                    'name' => $c->getName(),
                    'url' => $c->getDiscoverLink(null, false)
                );
            }
            $video = array(
                'id' => $media->getId(),
                'type' => 'media',
                'link' => $media->getMediaLink(),
                'image' => $media->getResizerCdnLink(),
                'escaped_title' => $media->getTitle(false, true),
                'title' => $media->getTitle(),
                'logline' => $media->getLogline(),
                'escaped_logline' => $media->getLogline(true),
                'user' => $media->getUser()->getUsername(),
                'user_profile' => $media->getUser()->getProfileLink(),
                'duration' => $media->getDuration(true),
                'comment_count' => count($media->getCommentsAbout()),
                'views' => $media->getViews(false),
                'location' => $media->getLocation(),
                'escaped_location' => $media->getLocation(false, true),
                'rate_up' => count($media->getRatings(true)),
                'rate_down' => count($media->getRatings(false)),
                'image_source' => $media->getSource(),
                'created' => $media->getCreated()->getTimestamp(),
                'url' => $media->getUrl(),
                'city' => $media->getCity()->getName(),
                'state' => $media->getProvince()->getName(),
                'categories' => $categories
            );
            if ($match['series_id']) {
                $series = $seriesMapper->find($match['series_id']);
                $video['series_name'] = $series->getName();
                $video['series_link'] = $series->getSeriesLink();
            }
            $data['media'][] = $video;
        }
        $this->getResponse()
            ->setCode(200)
            ->setSuccess(true)
            ->setData($data)
            ->setCount(count($data['media']));
        return new JsonModel($this->getResponse()->build());

    }

    public function slot1() {

    }
}