<?php
namespace Townspot\View\Helper;

use Zend\View\Helper\AbstractHelper;  
use Zend\ServiceManager\ServiceLocatorAwareInterface;  
use Zend\ServiceManager\ServiceLocatorInterface;  

class VideoBlock extends AbstractHelper implements ServiceLocatorAwareInterface  
{  
	protected $media;
	
    public function __invoke($mediaId,$carousel = false, $position = false)
    {
		$helperPluginManager = $this->getServiceLocator();
		$serviceManager = $helperPluginManager->getServiceLocator();  	
		$mediaMapper = new \Townspot\Media\Mapper($serviceManager);
		$html = '';
		if ($media = $mediaMapper->find($mediaId)) {
			$resizerLink	= $media->getResizerLink(342,247);
			if (!preg_match('/^http/',$resizerLink)) {
				$resizerLink = $this->getView()->linkCdn($resizerLink);
			}
$media_block = <<<EOT
<div class="video-preview{position}">
	<a href="{media_link}">
		<img class="img-responsive preview-img" src="{preview_image}" alt="{title_escaped}">
	</a>
	<div class="{caption_class}">
		<div class="video-title">
			<h3 title="{title_escaped}">
				<a href="{media_link}">
					<div class="dot-text">{title}</div>
				</a>
				<a href="{profile_link}">
					<div class="dot-text">by {username}</div>
				</a>
			</h3>
			<h3 title="{location}" class="dot-text">{location}</h3>
		</div>
		<div class="info-button{position}"
			title="" 
			data-original-title="<a href='{media_link}'>{title_escaped}</a> by <a href='{profile_link}'>{username}</a>"
			data-content="{location}<br/>{logline}<footer>
			<span class='video-duration'><i class='fa fa-clock-o'></i> {duration}</span> 
<span class='video-plays'><i class='fa fa-play'></i> {views}</span> 
<span class='video-thumbs-up'><i class='fa fa-thumbs-up'></i> {rating_up}</span> 
<span class='video-thumbs-down'><i class='fa fa-thumbs-down'></i> {rating_down}</span>
<span class='video-comments'><i class='fa fa-comments'></i> {comments}</span></footer>"
			data-toggle="popover">
			<i class="fa fa-info-circle"></i>
		</div>
	</div>
</div>
EOT;

			$html .= str_replace(
				array(	'{caption_class}',
						'{media_link}',
						'{preview_image}',
						'{title_escaped}',
						'{title}',
						'{profile_link}',
						'{username}',
						'{duration}',
						'{comments}',
						'{views}',
						'{logline}',
						'{location}',
						'{rating_up}',
						'{rating_down}',
						'{position}'
				),
				array(	(($carousel) ? 'carousel-caption' : 'video-caption'),
						$media->getMediaLink(),
						$resizerLink,
						htmlentities($media->getTitle()),
						$media->getTitle(),
						$media->getUser()->getProfileLink(),
						$media->getUser()->getUsername(),
						$media->getDuration(true),
						count($media->getCommentsAbout()),
						$media->getViews(),
						htmlentities($media->getLogline()),
						htmlentities($media->getLocation()),
						count($media->getRatings(true)),
						count($media->getRatings(false)),
						($position) ? ' ' . $position : 'in-row'
				),
				$media_block);
		}
		return $html;
    }
	
	/** 
     * Set the service locator. 
     * 
     * @param ServiceLocatorInterface $serviceLocator 
     * @return CustomHelper 
     */  
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)  
    {  
        $this->serviceLocator = $serviceLocator;  
        return $this;  
    }  
    /** 
     * Get the service locator. 
     * 
     * @return \Zend\ServiceManager\ServiceLocatorInterface 
     */  
    public function getServiceLocator()  
    {  
        return $this->serviceLocator;  
    }  	
}