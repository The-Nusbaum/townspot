<?php
namespace Townspot\SectionBlock;
use Townspot\Doctrine\Mapper\AbstractEntityMapper;

class Mapper extends AbstractEntityMapper
{
	protected $_repositoryName = "Townspot\SectionBlock\Entity";

	public function getSectionMediaByBlockName($blockName) 
	{
		$media = array();
		$episodeMapper = new \Townspot\SeriesEpisode\Mapper($this->getServiceLocator());
		if ($block = $this->findOneByBlockName($blockName)) {
			foreach ($block->getSectionMedia() as $m) {
				$object = $m->getMedia();
				$resizerLink 		= $object->getResizerLink(640,480);
				$escaped_title		= $object->getTitle(false,true);
				$comment_count		= count($object->getCommentsAbout());
				$escaped_logline	= $object->getLogline(true);
				$escaped_location	= $object->getLocation(false,true);
				$rate_up			= count($object->getRatings(true));
				$rate_down			= count($object->getRatings(false));
				if (!preg_match('/^http/',$resizerLink)) {
					$server = rand(1,9);
					$resizerLink = 'http://images' . $server . '.townspot.tv' . $resizerLink;
				}
				$_media = array(
					'id'				=> $object->getId(),
					'link'				=> $object->getMediaLink(),
					'image'				=> $resizerLink,
					'escaped_title'		=> $escaped_title,
					'title'				=> $object->getTitle(),
					'logline'			=> $object->getLogline(),
					'user'				=> $object->getUser()->getUsername(),
					'user_profile'		=> $object->getUser()->getProfileLink(),
					'duration'			=> $object->getDuration(true),
					'comment_count'		=> $comment_count,
					'views'				=> $object->getViews(),
					'escaped_logline'	=> $escaped_logline,
					'location'			=> $object->getLocation(),
					'escaped_location'	=> $escaped_location,
					'rate_up'			=> $rate_up,
					'rate_down'			=> $rate_down,
					'why_we_choose'		=> $object->getWhyWeChose(),
					'series_name'		=> '',
					'series_link'		=> '',
				);
				//Is Part of Series
			    if ($seriesEpisode = $episodeMapper->findOneByMedia($object)) {
					$_media['series_name'] = htmlentities($seriesEpisode->getSeries()->getName());
					$_media['series_link'] = $seriesEpisode->getSeries()->getSeriesLink();
				}
				$media[] = $_media;
			}
		}
		return $media;
	}
}
