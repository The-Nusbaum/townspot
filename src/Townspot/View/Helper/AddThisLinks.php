<?php
namespace Townspot\View\Helper;

use Zend\View\Helper\AbstractHelper;  
use Zend\ServiceManager\ServiceLocatorAwareInterface;  
use Zend\ServiceManager\ServiceLocatorInterface;  

class AddThisLinks extends AbstractHelper implements ServiceLocatorAwareInterface  
{  
	protected $media;
	
    protected $params;

    public function __invoke($url)
    {
		$html  = '<div class="addthis_toolbox addthis_default_style addthis_32x32_style pull-right nowrap">' . "\n";
		$html .= '<a class="addthis_button_facebook addthis_button_preferred_1 at300b" title="Facebook" href="#"><i class="fa fa-facebook"></i></a>' . "\n";
		$html .= '<a class="addthis_button_twitter addthis_button_preferred_2 at300b" title="Tweet" href="#"><i class="fa fa-twitter"></i></a>' . "\n";
		$html .= '<a href="https://plus.google.com/share?url=' . $url . '" target="_blank"><i class="fa fa-google"></i></a>' . "\n";
		$html .= '<a class="addthis_button_compact" href="#"> <i class="fa fa-share-alt"></i></a>' . "\n";
		$html .= '<div class="atclear"></div></div>' . "\n";
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