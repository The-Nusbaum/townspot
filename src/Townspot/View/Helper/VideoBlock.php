<?php
namespace Townspot\View\Helper;

use Zend\View\Helper\AbstractHelper;  
use Zend\ServiceManager\ServiceLocatorAwareInterface;  
use Zend\ServiceManager\ServiceLocatorInterface;  

class VideoBlock extends AbstractHelper implements ServiceLocatorAwareInterface  
{  
	protected $media;
	
    public function __invoke($media,$carousel = false, $position = 'in-row')
    {
		$html = '';
		$helperPluginManager	= $this->getServiceLocator();
		$serviceManager 		= $helperPluginManager->getServiceLocator();  	
		$resizerLink		= $media->getResizerLink(342,247);
		if (!preg_match('/^http/',$resizerLink)) {
			$resizerLink = $this->getView()->linkCdn($resizerLink);
		}
		$carousel_class		= ($carousel) ? 'carousel-caption' : 'video-caption';
		$escaped_title		= $media->getTitle(false,true);
		$comment_count		= count($media->getCommentsAbout());
		$escaped_logline	= $media->getLogline(true);
		$escaped_location	= $media->getLocation(false,true);
		$rate_up			= count($media->getRatings(true));
		$rate_down			= count($media->getRatings(false));
		$position_class		= ' ' . $position;
		
		$html .= "<div class=\"video-preview{position}\">\n";
		$html .= "	<a href=\"{media_link}\">\n";
		$html .= "		<img class=\"img-responsive preview-img\" src=\"{preview_image}\" alt=\"{title_escaped}\">\n";
		$html .= "	</a>\n";
		$html .= "	<div class=\"{caption_class}\">\n";
		$html .= "		<div class=\"video-title\">\n";
		$html .= "			<h3 title=\"{title_escaped}\">\n";
		$html .= "				<a href=\"{media_link}\">\n";
		$html .= "					<div class=\"dot-text\">{title}</div>\n";
		$html .= "				</a>\n";
		$html .= "				<a href=\"{profile_link}\">\n";
		$html .= "					<div class=\"dot-text\">by {username}</div>\n";
		$html .= "				</a>\n";
		$html .= "			</h3>\n";
		$html .= "			<h3 title=\"{location}\" class=\"dot-text\">{location}</h3>\n";
		$html .= "		</div>\n";
		$html .= "		<div class=\"info-button{position}\"\n";
		$html .= "			title=\"\" \n";
		$html .= "			data-original-title=\"<a href='{media_link}'>{title_escaped}</a> by <a href='{profile_link}'>{username}</a>\"\n";
		$html .= "			data-content=\"{location}<br/>{logline}<footer>\n";
		$html .= "			<span class='video-duration'><i class='fa fa-clock-o'></i> {duration}</span> \n";
		$html .= "			<span class='video-plays'><i class='fa fa-play'></i> {views}</span> \n";
		$html .= "			<span class='video-thumbs-up'><i class='fa fa-thumbs-up'></i> {rating_up}</span> \n";
		$html .= "			<span class='video-thumbs-down'><i class='fa fa-thumbs-down'></i> {rating_down}</span>\n";
		$html .= "			<span class='video-comments'><i class='fa fa-comments'></i> {comments}</span></footer>\"\n";
		$html .= "			data-toggle=\"popover\">\n";
		$html .= "			<i class=\"fa fa-info-circle\" data-track=\"click\" data-type=\"info\" data-value=\"{id}\"></i>\n";
		$html .= "		</div>\n";
		$html .= "	</div>\n";
		$html .= "</div>\n";

		$html = str_replace(
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
			array(	$carousel_class,
					$media->getMediaLink(),
					$resizerLink,
					$escaped_title,
					$media->getTitle(),
					$media->getUser()->getProfileLink(),
					$media->getUser()->getUsername(),
					$media->getDuration(true),
					$comment_count,
                    number_format($media->getViews()),
					$escaped_logline,
					$escaped_location,
					$rate_up,
					$rate_down,
					$position_class
			),
			$html);
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