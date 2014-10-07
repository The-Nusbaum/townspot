<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Api;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\InitializableInterface;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
		$app = $e->getParam('application'); 
		$app->getEventManager()->attach('dispatch', array($this, 'setLayout')); //

        $sm = $e->getApplication()->getServiceManager();

        $controllers = $sm->get('ControllerLoader');

        $controllers->addInitializer(function($controller, $cl) {
            if ($controller instanceof InitializableInterface) {
                $controller->init();
            }
        }); // false tells the loader to run this initializer after all others
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
			$viewModel->setTemplate('api/layout');
		}
	} 	
}
