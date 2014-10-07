<?php
//Data migration
$mysql_1 = new mysqli('localhost', 'root', 'root', 'townspot_dev');
$mysql_2 = new mysqli('localhost', 'root', 'root', 'tsz');
//Migrate User
$sql = "SELECT * FROM townspot_dev.users order by id";
if ($result = $mysql_1->query($sql)) {
	while ($row = $result->fetch_assoc()) {
		$sql2 = "SELECT * FROM tsz.city where id=" . $row['city_id'];
		$row['latitude'] = 0;
		$row['longitude'] = 0; 
		if ($result2 = $mysql_2->query($sql2)) {
			if ($row2 = $result2->fetch_assoc()) {
				$row['latitude'] = $row2['latitude'];
				$row['longitude'] = $row2['longitude'];
			}
		}
		$record = array(	'user_id'				=> $row['id'],
							'username'				=> mysqli_real_escape_string($mysql_1,$row['username']),
							'password'				=> mysqli_real_escape_string($mysql_1,$row['password']),
							'email'					=> mysqli_real_escape_string($mysql_1,$row['email']),
							'first_name'			=> mysqli_real_escape_string($mysql_1,$row['first_name']),
							'last_name'				=> mysqli_real_escape_string($mysql_1,$row['last_name']),
							'display_name'			=> mysqli_real_escape_string($mysql_1,$row['username']),
							'activation_string'		=> mysqli_real_escape_string($mysql_1,$row['activation_string']),
							'security_key'			=> mysqli_real_escape_string($mysql_1,$row['security_key']),
							'country_id'			=> 99,
							'province_id'			=> $row['state_id'],
							'city_id'				=> $row['city_id'],
							'neighborhood'			=> mysqli_real_escape_string($mysql_1,$row['neighborhood']),
							'about_me'				=> mysqli_real_escape_string($mysql_1,$row['about_me']),
							'interests'				=> mysqli_real_escape_string($mysql_1,$row['interests']),
							'description'			=> mysqli_real_escape_string($mysql_1,$row['description']),
							'website'				=> mysqli_real_escape_string($mysql_1,$row['website']),
							'image_url'				=> mysqli_real_escape_string($mysql_1,$row['image_url']),
							'upload_url'			=> mysqli_real_escape_string($mysql_1,$row['upload_url']),
							'allow_contact'			=> $row['allow_contact'],
							'terms_agreement'		=> $row['terms_agreement'],
							'email_notifications'	=> $row['email_notifications'],
							'artist_name'			=> mysqli_real_escape_string($mysql_1,$row['artistname']),
							'latitude'				=> $row['latitude'],
							'longitude'				=> $row['longitude'],
							'created'				=> $row['created'],
							'updated'				=> $row['modified'],
							'security_key_expires'	=> $row['security_key_expires'],
		);
		$fields = array();	$values = array();
		foreach ($record as $key => $value) {
			$fields[] = sprintf("`%s`",$key);
			$values[] = sprintf("'%s'",$value);
		}
		$mysql_2->query(sprintf("INSERT INTO tsz.user (%s) VALUES (%s);\n",
			implode(',',$fields),
			implode(',',$values)
		));
		if ($row['twitter']) {
			$mysql_2->query(sprintf("INSERT INTO tsz.user_social_media (`user_id`,`source`,`link`) VALUES (%d,'twitter','%s');\n",
				$row['id'],
				$row['twitter']
			));
		}
		if ($row['status'] == 'A') {
			if ($row['role'] == 'admin') {
				$mysql_2->query(sprintf("INSERT INTO tsz.user_role_linker (`user_id`,`role_id`) VALUES ('%s','Administrator');\n",
					$row['id']
				));
			} elseif ($row['role'] == 'artist') {
				$mysql_2->query(sprintf("INSERT INTO tsz.user_role_linker (`user_id`,`role_id`) VALUES ('%s','Artist');\n",
					$row['id']
				));
			} else {
				$mysql_2->query(sprintf("INSERT INTO tsz.user_role_linker (`user_id`,`role_id`) VALUES ('%s','User');\n",
					$row['id']
				));
			}
		} else {
			$mysql_2->query(sprintf("INSERT INTO tsz.user_role_linker (`user_id`,`role_id`) VALUES ('%s','Unconfirmed');\n",
				$row['id']
			));
		}
		if ($row['facebook_id']) {
			$mysql_2->query(sprintf("INSERT INTO tsz.user_oauth (`user_id`,`source`,`external_id`) VALUES (%d,'facebook','%s');\n",
				$row['id'],
				$row['facebook_id']
			));
		}
	}
}
//Migrate users_follows
$sql = "SELECT * FROM townspot_dev.users_follows";
if ($result = $mysql_1->query($sql)) {
	while ($row = $result->fetch_assoc()) {
		$mysql_2->query(sprintf("INSERT INTO tsz.user_follow (`user_id`,`target_id`,`share_email`) VALUES (%d,%d,%d);\n",
			$row['followee_id'],
			$row['follower_id'],
			$row['share_email']
		));
	}
}
//Migrate user_events
$sql = "SELECT * FROM townspot_dev.user_events";
if ($result = $mysql_1->query($sql)) {
	while ($row = $result->fetch_assoc()) {
		$mysql_2->query(sprintf("INSERT INTO tsz.user_event (`id`,`user_id`,`title`,`url`,`description`,`artistname`,`start`) VALUES (%d,%d,'%s','%s','%s','%s','%s');\n",
			$row['id'],
			$row['user_id'],
			$row['title'],
			$row['url'],
			$row['description'],
			$row['artistname'],
			$row['start']
		));
	}
}
//Migrate Artist_Comments
$sql = "SELECT * FROM townspot_dev.artist_comments";
if ($result = $mysql_1->query($sql)) {
	while ($row = $result->fetch_assoc()) {
		$mysql_2->query(sprintf("INSERT INTO tsz.artist_comment (`id`,`user_id`,`target_id`,`comment`,`created`) VALUES (%d,%d,%d,'%s','%s');\n",
			$row['id'],
			$row['commenter_id'],
			$row['user_id'],
			mysqli_real_escape_string($mysql_1,$row['comment']),
			$row['created']
		));
	}
}
//Migrate user_activities
$sql = "SELECT * FROM townspot_dev.user_activities";
if ($result = $mysql_1->query($sql)) {
	while ($row = $result->fetch_assoc()) {
		$mysql_2->query(sprintf("INSERT INTO tsz.user_activity (`id`,`user_id`,`artist_id`,`media_id`,`action`,`value`,`created`) VALUES (%d,%d,%d,%d,'%s','%s','%s');\n",
			$row['id'],
			$row['user_id'],
			$row['artist_id'],
			$row['video_id'],
			mysqli_real_escape_string($mysql_1,$row['action']),
			mysqli_real_escape_string($mysql_1,$row['value']),
			$row['created']
		));
	}
}
//Migrate Media
$sql = "SELECT * FROM townspot_dev.videos order by id";
if ($result = $mysql_1->query($sql)) {
	while ($row = $result->fetch_assoc()) {
		$sql2 = "SELECT * FROM tsz.city where id=" . $row['city_id'];
		$row['latitude'] = 0;
		$row['longitude'] = 0; 
		if ($result2 = $mysql_2->query($sql2)) {
			if ($row2 = $result2->fetch_assoc()) {
				$row['latitude'] = $row2['latitude'];
				$row['longitude'] = $row2['longitude'];
			}
		}
		$source = 'internal';
		if (preg_match('/youtube/',$row['video_url'])) {
			$source = 'youtube';
		} elseif (preg_match('/youtu\.be/',$row['video_url'])) {
			$source = 'youtube';
		}
		$record = array(	'id'					=> $row['id'],
							'title'					=> mysqli_real_escape_string($mysql_1,$row['title']),
							'media_type'			=> 'video',
							'source'				=> $source,
							'logline'				=> mysqli_real_escape_string($mysql_1,$row['logline']),
							'description'			=> mysqli_real_escape_string($mysql_1,$row['description']),
							'why_we_chose'			=> mysqli_real_escape_string($mysql_1,$row['why_we_choose']),
							'url'					=> mysqli_real_escape_string($mysql_1,$row['video_url']),
							'preview_image'			=> mysqli_real_escape_string($mysql_1,$row['preview_url']),
							'allow_contact'			=> $row['allow_contact'],
							'authorised'			=> $row['authorised'],
							'request_debut_time'	=> $row['request_debut_time'],
							'approved'				=> $row['approved'],
							'on_media_server'		=> $row['onMediaServer'],
							'views'					=> $row['views'],
							'user_id'				=> $row['user_id'],
							'admin_id'				=> $row['authoriseByAdmin'],
							'duration'				=> $row['duration'],
							'country_id'			=> 99,
							'province_id'			=> $row['state_id'],
							'city_id'				=> $row['city_id'],
							'neighborhood'			=> mysqli_real_escape_string($mysql_1,$row['neighborhood']),
							'latitude'				=> $row['latitude'],
							'longitude'				=> $row['longitude'],
							'created'				=> $row['created'],
							'debut_time'			=> $row['debut_time'],
		);
		$fields = array();	$values = array();
		foreach ($record as $key => $value) {
			$fields[] = sprintf("`%s`",$key);
			$values[] = sprintf("'%s'",$value);
		}
		$mysql_2->query(sprintf("INSERT INTO tsz.media (%s) VALUES (%s);\n",
			implode(',',$fields),
			implode(',',$values)
		));
	}
}
//Migrate Media_Categories
$sql = "SELECT * FROM townspot_dev.video_categories";
if ($result = $mysql_1->query($sql)) {
	while ($row = $result->fetch_assoc()) {
		$mysql_2->query(sprintf("INSERT INTO tsz.media_category_linker (`media_id`,`category_id`) VALUES (%d,%d);\n",
			$row['video_id'],
			$row['category_id']
		));
	}
}
//Migrate Media_Comments
$sql = "SELECT * FROM townspot_dev.comments";
if ($result = $mysql_1->query($sql)) {
	while ($row = $result->fetch_assoc()) {
		$mysql_2->query(sprintf("INSERT INTO tsz.media_comment (`id`,`target_id`,`user_id`,`comment`,`created`) VALUES (%d,%d,%d,'%s','%s');\n",
			$row['id'],
			$row['video_id'],
			$row['user_id'],
			mysqli_real_escape_string($mysql_1,$row['comment']),
			$row['created']
		));
	}
}
//Migrate Encodings
$sql = "SELECT * FROM townspot_dev.encodings";
if ($result = $mysql_1->query($sql)) {
	while ($row = $result->fetch_assoc()) {
		$mysql_2->query(sprintf("INSERT INTO tsz.encoding (`media_id`,`encoding_id`,`status`) VALUES (%d,%d,'%s');\n",
			$row['video_id'],
			$row['media_id'],
			mysqli_real_escape_string($mysql_1,$row['jobStatus'])
		));
	}
}
//Migrate Ratings
$sql = "SELECT * FROM townspot_dev.ratings";
if ($result = $mysql_1->query($sql)) {
	while ($row = $result->fetch_assoc()) {
		$mysql_2->query(sprintf("INSERT INTO tsz.rating (`media_id`,`user_id`,`rating`) VALUES (%d,%d,'%s');\n",
			$row['video_id'],
			$row['user_id'],
			mysqli_real_escape_string($mysql_1,$row['rating'])
		));
	}
}
//Migrate users_favorites
$sql = "SELECT * FROM townspot_dev.users_favorites";
if ($result = $mysql_1->query($sql)) {
	while ($row = $result->fetch_assoc()) {
		$mysql_2->query(sprintf("INSERT INTO tsz.user_favorite (`user_id`,`media_id`) VALUES (%d,%d);\n",
			$row['user_id'],
			$row['video_id']
		));
	}
}
//Migrate series
$series_seasons = array();
$sql = "SELECT * FROM townspot_dev.video_series";
if ($result = $mysql_1->query($sql)) {
	$season_id = 1;
	while ($row = $result->fetch_assoc()) {
		$mysql_2->query(sprintf("INSERT INTO tsz.series (`id`,`user_id`,`name`,`description`,`media_type`,`created`) VALUES (%d,%d,'%s','%s','video',NOW());\n",
			$row['id'],
			$row['user_id'],
			$row['name'],
			$row['description']
		));
		$mysql_2->query(sprintf("INSERT INTO tsz.series_season (`id`,`series_id`,`season_number`,`name`,`description`,`created`) VALUES (%d,%d,%d,'%s','%s',NOW());\n",
			$season_id,
			$row['id'],
			1,
			$row['name'],
			$row['description']
		));
		$series_seasons[$row['id']] = $season_id;
		$season_id++;
	}
}

//Migrate series
$sql = "SELECT * FROM townspot_dev.series_categories";
if ($result = $mysql_1->query($sql)) {
	while ($row = $result->fetch_assoc()) {
		$mysql_2->query(sprintf("INSERT INTO tsz.series_category_linker (`series_id`,`category_id`) VALUES (%d,%d);\n",
			$row['series_id'],
			$row['category_id']
		));
	}
}

//Migrate video_episodes
$sql = "SELECT * FROM townspot_dev.video_episodes";
if ($result = $mysql_1->query($sql)) {
	while ($row = $result->fetch_assoc()) {
		if (isset($series_seasons[$row['series_id']])) {
			$mysql_2->query(sprintf("INSERT INTO tsz.series_episodes (`season_id`,`media_id`,`episode_number`) VALUES (%d,%d,%d);\n",
				$series_seasons[$row['series_id']],
				$row['video_id'],
				$row['episodeNumber']
			));
		}
	}
}

//Migrate staff_favorites
$sql = "SELECT * FROM townspot_dev.staff_favorites";
if ($result = $mysql_1->query($sql)) {
	while ($row = $result->fetch_assoc()) {
		$mysql_2->query(sprintf("INSERT INTO tsz.section_media (`section_block_id`,`media_id`,`priority`) VALUES (%d,%d,%d);\n",
			3,
			$row['video_id'],
			$row['order']
		));
	}
}

//Migrate onstage
$sql = "SELECT * FROM townspot_dev.video_onstage";
if ($result = $mysql_1->query($sql)) {
	while ($row = $result->fetch_assoc()) {
		$mysql_2->query(sprintf("INSERT INTO tsz.section_media (`section_block_id`,`media_id`,`priority`) VALUES (%d,%d,%d);\n",
			1,
			$row['video_id'],
			$row['order']
		));
	}
}

//Migrate spotlight
$sql = "SELECT * FROM townspot_dev.video_spotlights";
if ($result = $mysql_1->query($sql)) {
	while ($row = $result->fetch_assoc()) {
		$mysql_2->query(sprintf("INSERT INTO tsz.section_media (`section_block_id`,`media_id`,`priority`) VALUES (%d,%d,%d);\n",
			2,
			$row['video_id'],
			$row['order']
		));
	}
}
