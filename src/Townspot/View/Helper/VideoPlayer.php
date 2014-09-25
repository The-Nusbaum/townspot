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
		$this->params = $params;
		if ($media = $mediaMapper->find($mediaId)) {
			$this->media = $media;
			$function = $media->getSource();
			return $this->{$function}();
		}
		return null;
    }
	
    public function internal()
    {
		$html  = sprintf("<div id='%s'></div>\n",$this->_getId());
		$html .= "<script type=\"text/javascript\">\n";
		$html .= $this->_jwplayerSetup();
		$html .= $this->_jwplayerError();
		$html .= "</script>\n";
		return $html;
	}

    public function youtube()
    {
		$html  = sprintf("<div id='%s'></div>\n",$this->_getId());
		$html .= "<script type=\"text/javascript\">\n";
		$html .= $this->_jwplayerSetup();
		$html .= $this->_jwplayerError();
		$html .= "</script>\n";
		return $html;
	}

    public function vimeo()
    {
	}
	
    protected function _jwplayerSetup()
    {
		$sections = array();
		$html  = sprintf("jwplayer('%s').setup({\n",$this->_getId());
		$html .= sprintf("width: \"%s\",\n",$this->_getWidth());
		$html .= sprintf("aspectratio: \"%s\",\n",$this->_getAspectRatio());
		$html .= sprintf("stretching: \"%s\",\n",$this->_getStretching());
		$media = $this->_getMedia();
		if (is_array($media)) {
			$_playlist = array();
			foreach ($media as $m) {
				$_media = "    {\n";
				$_media .= sprintf("    file: \"%s\",\n",$m['url']);
				$_media .= sprintf("    label: \"%s\"\n",$m['label']);
				$_media .= "    }";
				$_playlist[] = $_media;
			}
			$section  = "playlist: [{\n";
			$section .= sprintf("    image: \"%s\",\n",$this->_getPreviewImage());
			$section .= "sources: [\n" . implode(",\n",$_playlist) . ']' . "\n";
			$section .= '		}]' . "\n";
			$sections[] = $section;
		} else {
			$html .= sprintf("image: \"%s\",\n",$this->_getPreviewImage());
			$html .= sprintf("file: \"%s\",\n",$media);
		}
		if ($relatedLink = $this->_getRelatedLink()) {
			$section = "related: {\n";
			$section .= sprintf("    file: \"%s\",\n",$relatedLink);
			$section .= "    dimensions: '200x150'\n";
			$section .= "}\n";
			$sections[] = $section;
		}
		if ($sharingLink = $this->_getSharingLink()) {
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
	
    protected function _jwplayerError()
    {
		$html  = sprintf("jwplayer('%s').onSetupError(function(fallback,message) {\n",$this->_getId());
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

    protected function _getId()
	{
		return (@$this->params['id']) ?: 'media-player';
	}
	
    protected function _getWidth()
	{
		return (@$this->params['width']) ?: '100%';
	}
	
    protected function _getAspectRatio()
	{
		return (@$this->params['aspectratio']) ?: '16:10';
	}

    protected function _getStretching()
	{
		return (@$this->params['stretching']) ?: 'uniform';
	}

    protected function _getPreviewWidth()
	{
		return (@$this->params['preview_width']) ?: '640';
	}

    protected function _getPreviewHeight()
	{
		return (@$this->params['preview_height']) ?: '480';
	}
	
    protected function _getRelatedLink()
	{
		if (@$this->params['include_related']) {
			return '/videos/rss/relatedVideos?id=' . $this->media->getId();
		}
		return null;
	}

    protected function _getSharingLink()
	{
		if (@$this->params['include_sharing']) {
			return $this->media->getEmbedLink();
		}
		return null;
	}

    protected function _getPreviewImage()
	{
		$resizerLink	= $this->media->getResizerLink($this->_getPreviewWidth(),$this->_getPreviewHeight());
		if (!preg_match('/^http/',$resizerLink)) {
			$resizerLink = $this->getView()->linkCdn($resizerLink);
		}
		return $resizerLink;
	}

    protected function _getMedia()
	{
		if ($this->media->getSource() == 'internal') {
			return array(
				array(	'label'	=> 'HD',
						'url' 	=>  $this->media->getMediaUrl('HD')),
				array(	'label'	=> 'SD',
						'url' 	=>  $this->media->getMediaUrl('SD')),
				array(	'label'	=> 'Mobile',
						'url' 	=>  $this->media->getMediaUrl('Mobile')),
			);
		}
		return $this->media->getUrl();
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