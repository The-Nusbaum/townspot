<?php
namespace Townspot\View\Helper;

use Zend\View\Helper\AbstractHelper;  
use Zend\ServiceManager\ServiceLocatorAwareInterface;  
use Zend\ServiceManager\ServiceLocatorInterface;  
use Townspot\View\Helper\Video;  

class VideoCarousel extends AbstractHelper implements ServiceLocatorAwareInterface  
{  
	protected $media;
	
    protected $params;

    public function __invoke($media,$params=array())
    {
		$helperPluginManager 	= $this->getServiceLocator();
		$serviceManager 		= $helperPluginManager->getServiceLocator();  	
		$this->media 			= $media;
		$this->params 			= $params;
		$html  = sprintf("<div id=\"%s\" class=\"%s\" data-ride=\"carousel\">",$this->_getId(),$this->_getClass());
		$html .= sprintf("<div id=\"%s-inner\" class=\"carousel-inner\">",$this->_getId());
		$html .= $this->getItems();
		$html .= "</div>"; 
		$numberOfPanels = $this->_numberOfPanels();
		if ($numberOfPanels > 1) {
			$html .= sprintf("<a class=\"left carousel-control\" href=\"#%s\" data-slide=\"prev\">
				<i class=\"fa fa-chevron-left fa-stack-1x\"></i>
				</a>",
				$this->_getId());
			$html .= sprintf("<a class=\"right carousel-control\" href=\"#%s\" data-slide=\"next\">
				<i class=\"fa fa-chevron-right fa-stack-1x\"></i>
				</a>",
				$this->_getId());
			$html .= "<ol class=\"carousel-indicators\">";
			for ($i=0;$i < $numberOfPanels; $i++) {
				$html .= sprintf("<li data-target=\"#%s\" data-slide-to=\"%d\" class=\"%s\"></li>",
					$this->_getId(),
					$i,
					((!$i) ? " active" : "")
				);
			}
			$html .= "</ol>";
		}
		$html .= "</div>"; 
		return $html;
    }
	
    public function getItems()
    {
		$html = '';
		$numberOfPanels = $this->_numberOfPanels();
		for ($i=0;$i < $numberOfPanels; $i++) {
			$html .= "<div class=\"item" . ((!$i) ? " active" : "") . "\">";
			$html .= $this->getItem($i);
			$html .= "</div>";
		}
		return $html;
	}
	
    public function getItem($index = 0)
    {
		$numberOfItemsPerPanel = $this->_itemsPerPanel();
		$spanWidth = floor(12/$numberOfItemsPerPanel);
		$html  = "<div class=\"row\">";
		for ($i=0; $i < $numberOfItemsPerPanel; $i++) {
			$position = ($i == 0) 								? 'first' : 'in-row';
			$position = ($i == ($numberOfItemsPerPanel - 1)) 	? 'last' : $position;
			$html .= sprintf("<div class=\"col-xs-%d\">",$spanWidth);
			$mediaIndex = ($index * $numberOfItemsPerPanel) + $i;
			if (!isset($this->media[$mediaIndex])) {
				$mediaIndex = rand (0, (count($this->media) - 1));
			}
			$html .= $this->getView()->VideoBlock($this->media[$mediaIndex],true, $position);
			$html .= "</div>";
		}
		$html .= "</div>";
		return $html;
	}

    protected function _getId()
	{
		return (@$this->params['id']) ?: 'media-carousel';
	}

    protected function _getClass()
	{
		$class = 'carousel slide';
		if (@$this->params['class']) {
			$class .= ' ' . $this->params['class'];
		}
		return $class;
	}

    protected function _itemsPerPanel()
	{
		return (@$this->params['per_panel']) ?: 3;
	}
	
    protected function _numberOfPanels()
	{
		return ceil(count($this->media) / $this->_itemsPerPanel());
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