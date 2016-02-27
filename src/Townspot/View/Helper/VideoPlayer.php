<?php
namespace Townspot\View\Helper;

use Zend\View\Helper\AbstractHelper;  
use Zend\ServiceManager\ServiceLocatorAwareInterface;  
use Zend\ServiceManager\ServiceLocatorInterface;  

class VideoPlayer extends AbstractHelper implements ServiceLocatorAwareInterface  
{  
	protected $media;
	
	protected $params;

	public function __invoke($media,$params=array())
	{
		$helperPluginManager	= $this->getServiceLocator();
		$serviceManager 		= $helperPluginManager->getServiceLocator();  	
		$mediaMapper = new \Townspot\Media\Mapper($serviceManager);
		if (is_numeric($media)) {
			$media = $mediaMapper->find($media);
		}
		$media->incrViews();
		$mediaMapper->setEntity($media)->save();
		$this->params 			= $params;
		$this->media = $media;
		
		$function = $media->getSource();
		if($function == 'facebook') $function = 'internal';
		return $this->{$function}();
	}
	
	public function internal()
	{
		$html  = sprintf("<div id='%s'></div>\n",$this->_getId());
		$html .= $this->_playerInfo();
		$html .= "<script type=\"text/javascript\">\n";
		$html .= $this->_jwplayerSetup();
		$html .= $this->_jwplayerError();
		$html .= "</script>\n";
		return $html;
	}

	public function youtube()
	{
		$html  = sprintf("<div id='%s'></div>\n",$this->_getId());
		$html .= $this->_playerInfo();
		$html .= "<script type=\"text/javascript\">\n";
		$html .= $this->_jwplayerSetup();
		$html .= $this->_jwplayerError();
		$html .= "</script>\n";
		return $html;
	}

	public function dailymotion()
	{
    preg_match('/\/(x.*?)_/',$this->media->getUrl(),$matches);
    $id = $matches[1];

    $api = new \Dailymotion();
		$response = $api->get(
		    "/video/$id",
		    array('fields' => array('id', 'title', 'owner', 'allow_embed', 'embed_url', 'poster_url', 'thumbnail_url',
		    	'views_total', 'description', 'duration','aspect_ratio'))
		);

		$this->media->setViews($response['views_total']);
    $helperPluginManager	= $this->getServiceLocator();
    $serviceManager 		= $helperPluginManager->getServiceLocator();
    $mediaMapper = new \Townspot\Media\Mapper($serviceManager);
    $mediaMapper->setEntity($this->media)->save();

		$html  = sprintf("<iframe class='dmPlayer' src='http://www.dailymotion.com/embed/video/%s' frameborder='0'></iframe>",$id);
		$html .= sprintf("<script>var hRatio = %s</script>", $response['aspect_ratio']);
		$html .= $this->_playerInfo();
		return $html;	
	}

	public function vimeo()
	{
		preg_match("/\/([0-9]+)/",$this->media->getUrl(),$matches);
		$id = $matches[1];
    $vimeo = new \Vimeo\Vimeo('ac278d2d73248632ac83bf9fc43900876b9c12e0', '68c3d6ee56c6a66a2c4e6f05c06f0199f84b94c3');
    $token = '45dd4e70cfd1a1b307c683c1b5deff2a';
    $vimeo->setToken($token);
    $response = $vimeo->request("/videos/$id");


    $this->media->setViews($response['body']['stats']['plays']);
    $helperPluginManager	= $this->getServiceLocator();
    $serviceManager 		= $helperPluginManager->getServiceLocator();
    $mediaMapper = new \Townspot\Media\Mapper($serviceManager);
    $mediaMapper->setEntity($this->media)->save();

		$html  = sprintf("<iframe class='vimeoPlayer' src='https://player.vimeo.com/video/%s?api=1' frameborder='0'></iframe>",$id);
		$html .= $this->_playerInfo();
		return $html;
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

	protected function _playerInfo()
	{
		$html = null;
		$aId = $this->media->getUser()->getId();
		$mId = $this->media->getId();
		if ($this->_getVideoInfo()) {
			$html  = "<div id='video-info' class='row'>\n";
			$html .= "    <div class='container'>\n";
			$html .= "        <div class='row'>\n";
			$html .= "            <div class='col-xs-8 col-sm-11 nopadding-left'>\n";
			$html .= "                <div id='video-title'>\n";
			$html .= "                    <table>\n";
			$html .= "                        <tr>\n";
			$html .= "                            <td rowspan='2'>\n";
			$html .= "                                <h1><i class='fa fa-star-o fa-toggle-o' id='favorite-link' data-track='click' data-type='fav' data-value='$mId'></i></h1>\n";
			$html .= "                            </td>\n";
			$html .= "                            <td>\n";
			$html .= "                                <h1 class='videoTitle'>%s</h1>\n";
			$html .= "                            </td>\n";
			$html .= "                        </tr>\n";
			$html .= "                        <tr>\n";
			$html .= "                            <td id='video-author'>\n";
			$html .= "                                <h2 class='videoAuthor'><a href='%s'>%s</a></h2>\n";
			$html .= "                            </td>\n";
			$html .= "                        </tr>\n";
			$html .= "                    </table>\n";
			$html .= "                </div>\n";
			$html .= "            </div>\n";
			$html .= "            <div class='col-xs-4 col-sm-1 video-ratings pull-right'>\n";
			$html .= "                <table class='pull-right'>\n";
			$html .= "                    <tr>\n";
			$html .= "                        <th><a href='javascript:void(0)' data-toggle='modal' data-target='#flagVideo' data-track='click' data-type='flag' data-value='$mId'><i class='fa fa-flag' id='flag'></i></a></th>\n";
			$html .= "                        <th><i class='fa fa-thumbs-o-up' id='rate-up' data-track='click' data-type='rate' data-value='$mId'></i></th>\n";
			$html .= "                        <th><i class='fa fa-thumbs-o-down' id='rate-down' data-track='click' data-type='rate' data-value='$mId'></i></th>\n";
			$html .= "                    </tr>\n";
			$html .= "                    <tr>\n";
			$html .= "                        <td></td>\n";
			$html .= "                        <td><div id='up-ratings'>%d</div></td>\n";
			$html .= "                        <td><div id='down-ratings'>%d</div></td>\n";
			$html .= "                    </tr>\n";
			$html .= "                </table>\n";
			$html .= "            </div>\n";
			$html .= "        </div>\n";
			$html .= "        <div class='row'>\n";
			$html .= "            <div class='col-xs-6 interaction-buttons'>\n";
			if($this->media->getUser()->getAllowContact()) {
				$html .= "                <button class='btn interaction-button' id='contact-interaction' data-track='click' data-type='contact' data-value='$aId'>Contact</button>\n";
			}
			if($this->media->getUser()->getAllowHire()) {
				$html .= "                <button class='btn interaction-button' id='hire-interaction' data-track='click' data-type='hire' data-value='$aId'>Hire Artist</button>\n";
			}
			$html .= "                <button class='btn interaction-button' id='follow-interaction' data-track='click' data-type='fan' data-value='$aId'>Become a Fan</button>\n";
			$html .= "                " . $this->_getYtSubscribe();
			$html .= "            </div>\n";
			$html .= "            <div class='col-xs-6 video-views'>\n";
			$html .= "                <div class='pull-right videoViews'>\n";
			$html .= "                    View Count: %s\n";
			$html .= "                </div>\n";
			$html .= "            </div>\n";
			$html .= "        </div>\n";
			$html .= "    </div>\n";
			$html .= "</div>\n";
			$html .= "<div id='flagVideo' class='modal fade'>\n";
			$html .=   "<div class='modal-dialog'>\n";
			$html .=     "<div class='modal-content'>\n";
			$html .=       "<div class='modal-header'>\n";
			$html .=         "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>\n";
			$html .=         "<h4 class='modal-title'>Flag Video</h4>\n";
			$html .=       "</div>\n";
			$html .=       "<div class='modal-body'>\n";
			$html .=       "<div class='form-group'>\n";
			$html .= 				"<label>Reason:</label>\n";
			$html .= 				"<select class='reason form-control'>\n";
			$html .=   				"<option value=''> -- Please Select --</option>\n";
			$html .=   				"<option value='Copyright'>Copyright Infringement</option>\n";
			$html .=   				"<option value='Claim'>Claim This Video/Profile</option>\n";
			$html .=   				"<option value='Content'>Harassment/Inappropriate Content</option>\n";
			$html .=   				"<option value='other'>Other</option>\n";
			$html .= 				"</select>\n";
			$html .= 				"</div>\n";
			$html .= 				"<div class='details form-group' style='display: none;'>\n";
			$html .=   				"<label>Email</label>\n";
			$html .=   				"<input type='text' class='email form-control'>\n";
			$html .=   				"<label>Details</label>\n";
			$html .=   				"<input type='text' class='detailsText form-control'>\n";
			$html .= 				"</div>\n";
			$html .=       "</div>\n";
			$html .=       "<div class='modal-footer'>\n";
			$html .=         "<button type='button' class='btn btn-default' data-dismiss='modal'>Cancel</button>\n";
			$html .=         "<button type='button' class='btn btn-primary' disabled='disabled' class='submitFlag'>Report</button>\n";
			$html .=       "</div>\n";
			$html .=     "</div>\n";
			$html .=   "</div>\n";
			$html .= "</div>\n";
			$html = sprintf($html,
				$this->media->getTitle(),
				$this->media->getUser()->getProfileLink(),
				$this->media->getUser()->getUsername(),
				count($this->media->getRatings(true)),
				count($this->media->getRatings(false)),
				number_format($this->media->getViews())
				);
		}
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
			return '/videos/related/' . $this->media->getId();
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

	protected function _getVideoInfo()
	{
		return (@$this->params['include_info']) ?: false;
	}

	protected function _getVideoButtons()
	{
		return (@$this->params['include_buttons']) ?: false;
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


	protected function _getYtSubscribe()
	{
		if ($channelId = $this->media->getYtSubscriberChannelId()) {
			return '<script src="https://apis.google.com/js/platform.js"></script><div class="g-ytsubscribe" data-channelid="'.$channelId.'" data-layout="default" data-count="default"></div>';
		}
		return null;
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