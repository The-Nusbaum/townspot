<?php
namespace Townspot\Media;
use Townspot\Doctrine\Mapper\AbstractEntityMapper;
use \Doctrine\ORM\Query\ResultSetMapping;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Mapper extends AbstractEntityMapper
{
	protected $_repositoryName = "Townspot\Media\Entity";
	
	public function getClosest($lat,$long) 
	{
		$sql = "SELECT id, ( 3959 * acos( cos( radians(%s) ) * cos( radians( latitude ) ) ";
		$sql .= "* cos( radians( longitude ) - radians(%s) ) + ";
		$sql .= "sin( radians(%s) ) * sin(radians(latitude)) ) ) AS distance ";
		$sql .= "from media WHERE media.approved=1";
		$sql .= " HAVING distance < 10000 ";
		$sql .= "ORDER BY distance ";
		$sql .= "LIMIT 1;";
		$sql = sprintf($sql,$lat,$long,$lat);

		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		if ($results = $stmt->fetchAll()) {
			$results = array_shift($results);
			if ($media = $this->find($results['id'])) {
				return $media;
			}
		}
		return null;
	}

	public function getSlots($user_id) {
		$sql = "CALL getSlots($user_id)";
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		if ($results = $stmt->fetchAll()) {
		$stmt->closeCursor();
			$ids = array();
			foreach($results as $r) {
				$ids[] = $r['id'];
			}
			$media = $this->findById($ids);
			$out = array();

			foreach($media as $m) { /* @var $m \Townspot\Media\Entity */
				$_m = array(
					'comments'			=> $m->getCommentsAbout(false,true),
					'displayName'		=> $m->getUser()->getDisplayName(),
					'duration'			=> $m->getDuration(),
					'escaped_location'	=> $m->getLocation(false,true),
					'escaped_logline'	=> $m->getLogline(true),
					'escaped_title'		=> $m->getTitle(false,true),
					'id'				=> $m->getId(),
					'image'				=> $m->getPreviewImage(),
					'image_source'		=> "",
					'link'				=> $m->getMediaLink(),
					'location'			=> $m->getLocation(),
					'logline'			=> $m->getLogline(),
					'profileLink'		=> $m->getUser()->getProfileLink(),
					'rate_down'			=> $m->getRatings(-1,true),
					'rate_up'			=> $m->getRatings(1,true),
					'series_link'		=> "",
					'series_name'		=> "",
					'title'				=> $m->getTitle(),
					'type'				=> $m->getMediaType(),
					'user'				=> $m->getUser()->getDisplayName(),
					'user_profile'		=> $m->getUser()->getProfileLink(),
					'username'			=> $m->getUser()->getDisplayName(),
					'views'				=> $m->getViews(),
				);
				$out[] = $_m;

			}
			return $out;
		}
		return null;
	}

	public function slot1($user_id) {
		$sql = "SELECT slot1($user_id) as id";
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		if ($results = $stmt->fetchAll()) {
			$results = array_shift($results);
			if ($media = $this->find($results['id'])) {
				return $media;
			}
		}
		return null;
	}

	public function slot2() {
		$sql = "SELECT slot2() as id";
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		if ($results = $stmt->fetchAll()) {
			$results = array_shift($results);
			if ($media = $this->find($results['id'])) {
				return $media;
			}
		}
		return null;
	}

	public function slot3() {
		$sql = "SELECT slot3() as id";
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		if ($results = $stmt->fetchAll()) {
			$results = array_shift($results);
			if ($media = $this->find($results['id'])) {
				return $media;
			}
		}
		return null;
	}

	public function getIndexerRows($dateTime = null) 
	{
		$results = array();
		$sql  = "SELECT ";
		$sql .= " media.id, ";
		$sql .= " media.title, ";
		$sql .= " media.logline, ";
		$sql .= " media.description, ";
		$sql .= " media.user_id, ";
		$sql .= " media.city_id, ";
		$sql .= " media.province_id, ";
		$sql .= " media.views, ";
		$sql .= " media.created, ";
		$sql .= " GROUP_CONCAT(category.name SEPARATOR '::') as categories ";
		$sql .= "FROM  ";
		$sql .= " media ";
		$sql .= "JOIN media_category_linker on media_category_linker.media_id = media.id ";
		$sql .= "JOIN category on media_category_linker.category_id = category.id ";
		$sql .= "LEFT JOIN series_episodes on series_episodes.media_id = media.id ";
		$sql .= "WHERE media.approved = 1 ";
		if ($dateTime) {
			$sql .= " AND media.updated >= '" . $dateTime->format('Y-m-d H:i:s') . "'";
		}
		$sql .= " GROUP BY id";
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function getRandom() 
	{
		$results = array();
		$sql  = "SELECT ";
		$sql .= " media.id ";
		$sql .= "FROM  ";
		$sql .= " media ";
		$sql .= "WHERE media.approved = 1 ";
		$sql .= " ORDER BY RAND()";
		$sql .= " LIMIT 1";
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		if ($results = $stmt->fetchAll()) {
			$results = array_shift($results);
			if ($media = $this->find($results['id'])) {
				return $media;
			}
		}
		return null;
	}

	public function getMediaLike($object,$limit=3,$offset = 1)
	{
		$offset = $offset - 1;
		$results = array();
		$seriesId = 0;
		$seriesEpisodeMapper = new \Townspot\SeriesEpisode\Mapper($this->getServiceLocator());
		// Match Series
		$episode = $seriesEpisodeMapper->findByMedia($object);
		if (count($episode)) {
			$episode = array_shift($episode);
			$seriesId = $episode->getSeries()->getId();
			$episodes = $seriesEpisodeMapper->findBySeries($episode->getSeries());
			foreach ($episodes as $_episode) {
				if ($_episode->getEpisodeNumber() > $episode->getEpisodeNumber()) {
					if ($_episode->getMedia()->getApproved()) {
						$results[] = $_episode->getMedia()->getId();
					}
				}
			}
		}
		//Match User Series
		if (count($results) < $limit) {
			$_randResults = array();
			$sql  = "SELECT ";
			$sql .= " series_episodes.media_id ";
			$sql .= "FROM  ";
			$sql .= " series ";
			$sql .= " JOIN series_episodes on series_episodes.series_id = series.id ";
			$sql .= " JOIN media on series_episodes.media_id = media.id ";
			$sql .= "WHERE series.user_id = " . $object->getUser()->getId();
			$sql .= " AND series.id != " . $seriesId;
			$sql .= " AND series_episodes.episode_number = 1";
			$sql .= " AND media.approved = 1";
			$sql .= " ORDER BY RAND()";
			$sql .= " LIMIT " . $limit;
			$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
			$stmt->execute();
			if ($result = $stmt->fetchAll()) {
				foreach ($result as $_result) {
					$results[] = $_result['media_id'];
				}
			}
		}
		if (count($results) < $limit) {
			$sql  = "SELECT ";
			$sql .= " media.id ";
			$sql .= "FROM  ";
			$sql .= " media ";
			$sql .= "WHERE media.user_id = " . $object->getUser()->getId();
			$sql .= " AND media.id != " . $object->getId();
			$sql .= " AND media.approved = 1";
			$sql .= " ORDER BY RAND()";
			$sql .= " LIMIT " . $limit;
			$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
			$stmt->execute();
			if ($result = $stmt->fetchAll()) {
				foreach ($result as $_result) {
					$results[] = $_result['id'];
				}
			}
		}
		if (count($results) < $limit) {
			//Get Categories
			$sql  = "SELECT category_id from media_category_linker where media_id=" . $object->getId();
			$categories = array();
			$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
			$stmt->execute();
			if ($result = $stmt->fetchAll()) {
				foreach ($result as $_result) {
					$categories[] = $_result['category_id'];
				}
			}
			$sql  = "SELECT DISTINCT media_id from media_category_linker";
			$sql .= " JOIN media on media_category_linker.media_id = media.id ";
			$sql .= " WHERE media_category_linker.category_id IN (" . implode(',',$categories) . ")";
			$sql .= " AND media.id != " . $object->getId();
			$sql .= " AND media.approved = 1";
			$sql .= " ORDER BY RAND()";
			$sql .= " LIMIT " . $limit;
			$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
			$stmt->execute();
			if ($result = $stmt->fetchAll()) {
				foreach ($result as $_result) {
					$results[] = $_result['media_id'];
				}
			}
		}
		$results = array_slice($results,0,$limit);
		foreach ($results as $index => $media_id) {
			$results[$index] = $this->find($media_id);
		}
		return $results;
	}
	
	public function getDiscoverMedia($country_id = null, $province_id = null,$city_id=null,$category_id = null)
	{
		$results = array();
		$sql  = "SELECT ";
		$sql .= " DISTINCT media.id, series.id as series_id ";
		$sql .= "FROM  ";
		$sql .= " media ";
		$sql .= "JOIN media_category_linker on media_category_linker.media_id = media.id ";
		$sql .= "LEFT JOIN series_episodes on series_episodes.media_id = media.id ";
		$sql .= "LEFT JOIN series on series_episodes.series_id = series.id ";
		$where = array('media.approved = 1');
		if ($country_id) {
			$where[] = 'media.country_id=' . $country_id;
		}
		if ($province_id) {
			$where[] = 'media.province_id=' . $province_id;
		}
		if ($city_id) {
			$where[] = 'media.city_id=' . $city_id;
		}
		if ($category_id) {
			$where[] = 'media_category_linker.category_id=' . $category_id;
		}
		if ($where) {
			$sql .= " WHERE " . implode(' AND ',$where);
		}
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}

    public function getChannelsurfMedia($state = null,$city = null,$category = null,$limit = null,$notThese = null,$sort = null)
    {
        $results = array();
        $sql  = "SELECT ";
        $sql .= " DISTINCT media.id, series.id as series_id ";
        $sql .= "FROM  ";
        $sql .= " media ";
        $sql .= "JOIN media_category_linker on media_category_linker.media_id = media.id ";
        $sql .= "LEFT JOIN series_episodes on series_episodes.media_id = media.id ";
        $sql .= "LEFT JOIN series on series_episodes.series_id = series.id ";
        $sql .= 'WHERE media.approved = 1';
        if($state) $sql .= " AND media.province_id = $state";
        if($city) $sql .= " AND media.city_id = $city";
        if($category) $sql .= " AND media_category_linker.category_id = $category";
        if($notThese) {
            $notThese = implode(',',$notThese);
            $sql .= " AND media.id NOT IN($notThese)";
        }
        if($sort) {
            $sql .= " ORDER BY $sort";
        } else {
            $sql .= ' ORDER BY RAND()';
        }
        if ($limit) $sql .= " LIMIT $limit";

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
	
	public function getStats() 
	{
		$sql  = "SELECT count(*) as media_count, ";
        $sql .= "SUM(views) as view_count, ";
        $sql .= "(SELECT count(*) from media_comment) as comment_count, ";
        $sql .= "(SELECT count(*) from rating where rating.rating=1) as up_rating_count, ";
        $sql .= "(SELECT count(*) from rating where rating.rating=-1) as down_rating_count ";
		$sql .= "FROM tsz.media";
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		return $stmt->fetch();
	}

	public function getTopStats($count = 10) 
	{
		$sql  = "SELECT id,title,views ";
		$sql .= "FROM tsz.media ";
		$sql .= "ORDER BY views DESC ";
		$sql .= "LIMIT " . $count;
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function getAdminList($options = array()) 
	{
		$where = array();
		$sort_field = 'media.id';
		$sort_order = 'ASC';
		$sql  = "SELECT media.id,
						media.title,
						CONCAT(series.name, ' : ', series_episodes.episode_number) as `series`,
						user.username as username,
						CONCAT(city.name, ',', province.abbrev, ',', country.code2) as location,
						media.views,
						media.created as added,
						media.approved as status
						from media
					JOIN user on user.user_id = media.user_id
					LEFT JOIN country on media.country_id = country.id
					LEFT JOIN city on media.city_id = city.id
					LEFT JOIN province on media.province_id = province.id
					LEFT JOIN series_episodes on series_episodes.media_id = media.id
					LEFT JOIN series on series_episodes.series_id = series.id";
		foreach ($options as $key => $value) {
			if (($value !== null)&&($value !== '')) {
				switch ($key) {
					case 'title':
						$where[] = "media.title LIKE '" . $value . "%'";
						break;
					case 'username':
						$where[] = "user.username LIKE '" . $value . "%'";
						break;
					case 'province':
						$where[] = "media.province_id = " . $value;
						break;
					case 'city':
						$where[] = "media.city_id = " . $value;
						break;
					case 'after':
						$where[] = "media.created >= '" . date('Y-m-d H:i:s',strtotime($value)) . "'";
						break;
					case 'before':
						$where[] = "media.created <= '" . date('Y-m-d H:i:s',strtotime($value)) . "'";
						break;
					case 'status':
						$where[] = "media.approved = " . $value;
						break;
					case 'sort_field':
						$sort_field = $value;
						break;
					case 'sort_order':
						$sort_order = $value;
						break;
				}
			}
		}
		if ($where) {
			$sql .= " WHERE " . implode(' AND ',$where);
		} 
		$sql .= " ORDER BY " . $sort_field . " " . $sort_order;
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}
	
	public function delete() 
	{
        $entity = $this->getEntity();
		$sql  = "DELETE from encoding where encoding.media_id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$sql  = "DELETE from media_category_linker where media_category_linker.media_id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$sql  = "DELETE from media_comment where media_comment.target_id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$sql  = "DELETE from media_schedule where media_schedule.media_id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$sql  = "DELETE from media_tag where media_tag.media_id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$sql  = "DELETE from rating where rating.media_id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$sql  = "DELETE from section_media where section_media.media_id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$sql  = "DELETE from series_episodes where series_episodes.media_id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$sql  = "DELETE from user_activity where user_activity.media_id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$sql  = "DELETE from user_favorite where user_favorite.media_id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$sql  = "DELETE from media where media.id=" . $entity->getId();
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
	}
	
	public function getAvailableMedia($options = array()) 
	{
		$where = array('media.approved=1');
		$sort_field = 'media.id';
		$sort_order = 'ASC';
		$sql  = "SELECT DISTINCT media.id,
						media.title,
						user.username as username
						from media
					JOIN user on user.user_id = media.user_id
					JOIN media_category_linker on media_category_linker.media_id = media.id";
		foreach ($options as $key => $value) {
			if (($value !== null)&&($value !== '')) {
				switch ($key) {
					case 'title':
						$where[] = "media.title LIKE '" . $value . "%'";
						break;
					case 'username':
						$where[] = "user.username LIKE '" . $value . "%'";
						break;
					case 'user_id':
						$where[] = "media.user_id LIKE '" . $value . "'";
						break;
					case 'category':
						$where[] = "media_category_linker.category_id = " . $value;
						break;
					case 'sort_field':
						switch ($value) {
							case 'id':
								$sort_field = 'media.id';
								break;
							case 'username':
								$sort_field = 'user.username';
								break;
							case 'title':
								$sort_field = 'media.title';
								break;
						}
						break;
					case 'sort_order':
						$sort_order = $value;
						break;
				}
			}
		}
		if ($where) {
			$sql .= " WHERE " . implode(' AND ',$where);
		} 
		$sql .= " ORDER BY " . $sort_field . " " . $sort_order;
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}

    public function save() {
        parent::save();
        $amqp = $this->getServiceLocator()->get('Config')['amqp'];
        $encoding = $this->getServiceLocator()->get('Config')['encoding'];

        $queue  = 'video.updated';
        $exchange =  'video';

        $conn = new AMQPStreamConnection(
            $amqp['host'],
            $amqp['port'],
            $amqp['user'],
            $amqp['pass'],
            $amqp['vhost']
        );
        $ch = $conn->channel();
        $ch->queue_declare($queue, false, true, false, false);
        $ch->exchange_declare($exchange, 'direct', false, true, false);
        $ch->queue_bind($queue, $exchange);

        $media = $this->getEntity();

        $fileParts = explode('.',$media->getPreviewImage());
        $imageExt = array_pop($fileParts);
        $fileParts = explode('.',$media->getUrl());
        $videoExt = array_pop($fileParts);
        $msg_body = json_encode(array(
            'id' => $media->getId(),
            'preview_url' => $media->getPreviewImage(),
            'video_url' => $media->getUrl(),
            'host' => $encoding['sshHost'],
            'webroot' => APPLICATION_PATH . '/public',
            'imageFileName' => $media->getId().".$imageExt",
            'videoFileName' => $media->getId().".$videoExt"
        ));
        $msg = new AMQPMessage($msg_body, array('content_type' => 'text/plain', 'delivery_mode' => 2));
        $ch->basic_publish($msg, $exchange);
    }
}
