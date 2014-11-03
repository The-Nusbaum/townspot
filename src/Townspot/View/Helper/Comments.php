<?php
namespace Townspot\View\Helper;

use Zend\View\Helper\AbstractHelper;  
use Zend\ServiceManager\ServiceLocatorAwareInterface;  
use Zend\ServiceManager\ServiceLocatorInterface;  

class Comments extends AbstractHelper implements ServiceLocatorAwareInterface  
{  
    public function __invoke($comments)
    {
		$html  = "<ul id=\"comment-list\">\n";
		if (count($comments)) {
			foreach ($comments as $comment) {
				$html .= sprintf("<li data-id=\"%d\" class=\"comment\" id=\"comment-%d\">",
					$comment->getId(),
					$comment->getId());
				$html .= "<div class=\"row\"><div class=\"col-xxs-2\">";
				$html .= sprintf("<img class=\"img-responsive\" src=\"%s\">",
					$this->getView()->linkCdn($comment->getUser()->getProfileImage()));
				$html .= "</div>";
				$html .= sprintf("<div class=\"col-xxs-10 comment-comment\">%s</div>",
					$comment->getComment());
				$html .= sprintf("<a href=\"%s\">%s</a> - <abbr class=\"timeago\" title=\"%s\">",
					$comment->getUser()->getProfileLink(),				
					$comment->getUser()->getUsername(),
					$comment->getCreated()->format('c'));
				$html .= "</div>";
				$html .= "</li>";
			}	
		}
		$html .= "</ul>\n";
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