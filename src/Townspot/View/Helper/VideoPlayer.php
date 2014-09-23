<?php
namespace Townspot\View\Helper;

use Zend\View\Helper\AbstractHelper;  
use Zend\ServiceManager\ServiceLocatorAwareInterface;  
use Zend\ServiceManager\ServiceLocatorInterface;  

class VideoPlayer extends AbstractHelper implements ServiceLocatorAwareInterface  
{  
	protected $media;
	
    protected $params;

    public function __invoke($mediaId,$params=array())
    {
		$helperPluginManager = $this->getServiceLocator();  
		$serviceManager = $helperPluginManager->getServiceLocator();  	

		$mediaMapper = new \Townspot\Media\Mapper($serviceManager);

		if ($params) {
			$this->params = $params;
		}
		if ($media = $mediaMapper->find($mediaId)) {
			$this->media = $media;
			$function = $media->getSource();
			return $this->{$function}();
		}
		return null;
    }
	
    public function internal()
    {
		$id 			= (@$this->params['id']) 				?: 'media-player';
		$width 			= (@$this->params['width']) 			?: '100%';
		$aspectratio 	= (@$this->params['aspectratio']) 		?: '16:10';
		$stretching 	= (@$this->params['stretching']) 		?: 'uniform';
		$previewWidth 	= (@$this->params['preview_width']) 	?: '640';
		$previewHeight	= (@$this->params['preview_height'])	?: '480';
		$includeRelated	= (@$this->params['include_related'])	?: false;
		$includeSharing	= (@$this->params['include_sharing'])	?: false;
		$resizerLink	= $this->media->getResizerLink($previewWidth,$previewHeight);
		$relatedLink	= ($includeRelated) ? '/videos/rss/relatedVideos?id=' . $this->media->getId() : null;
		$sharingLink	= ($includeSharing) ? $this->media->getEmbedLink() : null;
		$playlist = array(
			array(	'label'	=> 'HD',
					'url' 	=>  $this->media->getMediaUrl('HD')),
			array(	'label'	=> 'SD',
					'url' 	=>  $this->media->getMediaUrl('SD')),
			array(	'label'	=> 'Mobile',
					'url' 	=>  $this->media->getMediaUrl('Mobile')),
		);
		$html  = sprintf("<div id='%s'></div>\n",$id);
		$html .= "<script type=\"text/javascript\">\n";
		$html .= $this->_jwplayerSetup($id,$width,$aspectratio,$stretching,$resizerLink,$playlist,$relatedLink,$sharingLink);
		$html .= $this->_jwplayerError($id);
		$html .= "</script>\n";
		return $html;
	}

    public function youtube()
    {
		$id 			= (@$this->params['id']) 				?: 'media-player';
		$width 			= (@$this->params['width']) 			?: '100%';
		$aspectratio 	= (@$this->params['aspectratio']) 		?: '16:10';
		$stretching 	= (@$this->params['stretching']) 		?: 'uniform';
		$previewWidth 	= (@$this->params['preview_width']) 	?: '640';
		$previewHeight	= (@$this->params['preview_height'])	?: '480';
		$MediaLink		= $this->media->getUrl();
		//@TODO: Is Image local
		$ResizerLink	= $this->media->getResizerLink($previewWidth,$previewHeight);
		$relatedLink	= '/videos/rss/relatedVideos?id=' . $this->media->getId();
		$sharingLink	= $this->media->getEmbedLink();
	}

    public function vimeo()
    {
		$id 			= (@$this->params['id']) 				?: 'media-player';
		$width 			= (@$this->params['width']) 			?: '100';
		$aspectratio 	= (@$this->params['aspectratio']) 		?: '16:10';
		list($_width,$_height) = explode(':',$aspectratio);
		$ratio = $_height/$_width;
		$width 			= $width * $ratio;
	}
	
    protected function _jwplayerSetup($id,$width,$aspectratio,$stretching,$image,$playlist,$relatedLink=null,$sharingLink=null)
    {
		$_playlist = array();
		$sections = array();
		foreach ($playlist as $media) {
			$_media = "    {\n";
			$_media .= sprintf("    file: \"%s\",\n",$media['url']);
			$_media .= sprintf("    label: \"%s\"\n",$media['label']);
			$_media .= "    }";
			$_playlist[] = $_media;
		}

		$html  = sprintf("jwplayer('%s').setup({\n",$id);
		$html .= sprintf("width: \"%s\",\n",$width);
		$html .= sprintf("aspectratio: \"%s\",\n",$aspectratio);
		$html .= sprintf("stretching: \"%s\",\n",$stretching);
		$section  = "playlist: [{\n";
		$section .= sprintf("    image: \"%s\",\n",$image);
		$section .= "sources: [\n" . implode(",\n",$_playlist) . ']' . "\n";
		$section .= '		}]' . "\n";
		$sections[] = $section;
		if ($relatedLink) {
			$section = "related: {\n";
			$section .= sprintf("    file: \"%s\",\n",$relatedLink);
			$section .= "    dimensions: '200x150'\n";
			$section .= "}\n";
			$sections[] = $section;
		}
		if ($sharingLink) {
			$section = "sharing: {\n";
			$section .= sprintf("    code: encodeURI('<iframe src=\"%s\" width=\"640\" height=\"426\" frameborder=\"0\" allowfullscreen></iframe>'),\n",
				$sharingLink);
			$section .= "},\n";
			$sections[] = $section;
		}
		$html .= implode(",",$sections);
		$html .= "});\n";
		return $html;
	}
	
    protected function _jwplayerError($id)
    {
	return null;
		$html  = sprintf("jwplayer('%s').onSetupError(function(fallback,message) {\n",$id);
		$html .= "    var hasFlash = false;\n";
		$html .= "    try {\n";
		$html .= "        var fo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');\n";
		$html .= "        if (fo) {\n";
		$html .= "            hasFlash = true;\n";
		$html .= "        }\n";
		$html .= "    } catch (e) {\n";
		$html .= "        if (navigator.mimeTypes\n";
		$html .= "         && navigator.mimeTypes['application/x-shockwave-flash'] != undefined\n";
		$html .= "         && navigator.mimeTypes['application/x-shockwave-flash'].enabledPlugin) {\n";
		$html .= "            hasFlash = true;\n";
		$html .= "        }\n";
		$html .= "    }\n";			
		$html .= "    if (hasFlash == false) {\n";
		$html .= "        message = \"Your browser currently does not have the Flash media plug-in needed to view the video. <br/>\";\n";
		$html .= "        message = message + \"Please download the plugin from <a href='http://get.adobe.com/flashplayer/' target='_blank'>Adobe - Install Adobe Flash Player</a>\";\n";
		$html .= "    }\n";
		$html .= "    $('#SetupErrorMessage').html(message);\n";
		$html .= "    $('#SetupError').modal('show')\n";			
		$html .= "    });\n";
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