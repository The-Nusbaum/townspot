<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ReportingController extends AbstractActionController
{
  public function __construct() {

	}

  public function isAuthenticated() {
		if (!$this->zfcUserAuthentication()->hasIdentity()) {
			return $this->redirect()->toUrl('/');
		}
		if (!$this->isAllowed('admin')) {
			return $this->redirect()->toUrl('/');
		}
	}
	
  public function indexAction()
  {
		$this->isAuthenticated();
  }

  public function trackingAction() {
  	$types = array(
  		"channel_surf" => "Channel Surf Clicked",
			"comment" => "Comment Left",
			"info" => "Info Clicked",
			"search" => "Searches Executed",
			"fb_login" => "Facebook Logins",
			"twitter_login" => "Twitter Logins",
			"fav" => "Videos Favorited",
			"flag" => "Videos Flagged",
			"rate" => "Videos Rated",
			"contact" => "Artists Contacted",
			"hire" => "Artists Contacted for Hire",
			"fan" => "Artists Fan'd",
			"internal_upload" => "Internal Submissions",
			"youtube_upload" => "Youtube Submissions",
			"dm_upload" => "Daily Motion Submissions",
			"vimeo_upload" => "Vimeo Submissions",
  	);

  	$trackingMapper = new \Townspot\Tracking\Mapper($this->getServiceLocator());

  	$sql = "(
				SELECT 'Today', count(*) as total FROM tsz.tracking where type = :type and created > curdate()
			) union (
				SELECT 'Last 30 Days', count(*) as total  FROM tsz.tracking where type = :type and created > curdate() - INTERVAL 30 day
			) union (
				SELECT 'All time', count(*) as total FROM tsz.tracking where type = :type
			)";

  	$query = $trackingMapper->getEntityManager()->getConnection()->prepare($sql);
  	
  	$data = array();
  	foreach($types as $type => $label){
  		$query->execute(array(':type'=> $type));
  		$data[$label] = $query->fetchAll(\PDO::FETCH_COLUMN, 1);  		
  	}

  	return new ViewModel( compact('data'));

  }
}
