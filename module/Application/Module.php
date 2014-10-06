<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager       	= $e->getApplication()->getEventManager();
		$serviceManager 		= $e->getApplication()->getServiceManager();
		$moduleRouteListener 	= new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
		$app = $e->getParam('application'); 
        // Add ACL information to the Navigation view helper
        $authorize 				= $serviceManager->get('/BjyAuthorize/Service/Authorize');
        $acl 					= $authorize->getAcl();
        $role 					= $authorize->getIdentity();
        \Zend\View\Helper\Navigation::setDefaultAcl($acl);
        \Zend\View\Helper\Navigation::setDefaultRole($role);		
		$app->getEventManager()->attach('dispatch', array($this, 'setLayout')); // 
		$serviceManager->get('viewhelpermanager')->setFactory('controllerName', function($sm) use ($e) {
			$viewHelper = new \Townspot\View\Helper\ControllerName($e->getRouteMatch());
			return $viewHelper;
		});
		$serviceManager->get('viewhelpermanager')->setFactory('actionName', function($sm) use ($e) {
			$viewHelper = new \Townspot\View\Helper\ActionName($e->getRouteMatch());
			return $viewHelper;
		});
		$moduleRouteListener = new ModuleRouteListener();
		$moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
	
	public function setLayout($e) {
		$matches = $e->getRouteMatch();
		$controller = $matches->getParam('controller');
		if (false !== strpos($controller, __NAMESPACE__)) {
			// Set the layout template
			$viewModel = $e->getViewModel();
			$viewModel->setTemplate('application/layout');
		}
	} 	
	
}
