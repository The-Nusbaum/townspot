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
use ZendGData\App\Exception;

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
		$serviceManager->get('viewhelpermanager')->setFactory('getmapper', function($sm) use ($e) {
		print $sm;
		die;
		
		
		});

        $events = $eventManager->getSharedManager();
		
        $events->attach('ZfcUser\Form\Login','init', function($e) {
            $form = $e->getTarget();
            $elements = $form->getElements();
            foreach ($elements as $element => $e) {
                switch($element) {
                    case 'identity':
                        $label = 'Username or Email';
                        break;
                    case 'credential':
                        $label = 'Password';
                        break;
                    default:
                        $label = '';
                }
                $e->setAttributes(array(
                    'placeholder'   => $label,
                    'class'         => "form-control",
                    'maxlength'     => 50,
                ));

            }
        });

        $events->attach('LfjOpauth\Service\OpauthService', \LfjOpauth\LfjOpauthEvent::EVENT_LOGIN_CALLBACK, function($e) {
            /** @var \Zend\Authentication\Result $result */
            $authenticationResult = $e->getParam('authenticationResult');

            /** @var \Zend\Authentication\AuthenticationService $authenticationService */
            $authenticationService = $e->getParam('authenticationService');

            /** @var \LfjOpauth\Service\OpauthService $target */
            $target = $e->getTarget();

            $provider = $e->getParam('provider');
/*
            echo '<pre>';
            var_dump(get_class($e->getTarget()));
            var_dump($e->getParam('provider'));
            var_dump('$authenticationResult->isValid()', $authenticationResult->isValid());
            var_dump('$authenticationService->hasIdentity()', $authenticationService->hasIdentity());
            var_dump('$authenticationService->getIdentity()', $authenticationService->getIdentity());
            var_dump('$authenticationResult->getCode()', $authenticationResult->getCode());
            var_dump('$authenticationResult->getIdentity()', $authenticationResult->getIdentity());
            var_dump('$authenticationResult->getMessages()', $authenticationResult->getMessages());
*/

            if($authenticationResult->isValid() && $authenticationService->hasIdentity()) {
                $userOauthMapper = new \Townspot\UserOauth\Mapper($e->getTarget()->getServiceLocator());
                try {
                    $external_id = $authenticationResult->getIdentity()['lfjopauth']['opauth'][$provider]["auth"]["uid"];
                    $userOauth = $userOauthMapper->findOneByExternalId($external_id);

                    if (empty($userOauth)) {
                        throw new Exception('No Linked Account Found');
                    }
                    $user = $userOauth->getUser();
                    $loginResult = $authenticationService->authenticate(new \Townspot\Authentication\Adapter\ForceLogin($user));
                } catch (Exception $ex) {
                    $flashMessenger = new \Zend\Mvc\Controller\Plugin\FlashMessenger();
                    $flashMessenger->addMessage('No Linked Account Found...');
                    $authenticationService->clearIdentity();
                    $url = $e->getTarget()->getRouter()->assemble(array(), array('name' => 'login'));
                    $response=$e->getTarget()->getServiceLocator()->get('Application')->getResponse();
                    $response->getHeaders()->addHeaderLine('Location', $url);
                    $response->setStatusCode(302);
                    $response->sendHeaders();
                    // When an MvcEvent Listener returns a Response object,
                    // It automatically short-circuit the Application running
                    // -> true only for Route Event propagation see Zend\Mvc\Application::run

                    // To avoid additional processing
                    // we can attach a listener for Event Route with a high priority
                    $stopCallBack = function($event) use ($response){
                        $event->stopPropagation();
                        return $response;
                    };
                    //Attach the "break" as a listener with a high priority
                    $e->getTarget()->getServiceLocator()->get('Application')->getEventManager()->attach(MvcEvent::EVENT_ROUTE, $stopCallBack,-10000);
                    return $response;
                }
                $url = $e->getTarget()->getRouter()->assemble(array(), array('name' => 'dashboard'));
                $response=$e->getTarget()->getServiceLocator()->get('Application')->getResponse();
                $response->getHeaders()->addHeaderLine('Location', $url);
                $response->setStatusCode(302);
                $response->sendHeaders();
                // When an MvcEvent Listener returns a Response object,
                // It automatically short-circuit the Application running
                // -> true only for Route Event propagation see Zend\Mvc\Application::run

                // To avoid additional processing
                // we can attach a listener for Event Route with a high priority
                $stopCallBack = function($event) use ($response){
                    $event->stopPropagation();
                    return $response;
                };
                //Attach the "break" as a listener with a high priority
                $e->getTarget()->getServiceLocator()->get('Application')->getEventManager()->attach(MvcEvent::EVENT_ROUTE, $stopCallBack,-10000);
                return $response;
            }

        });

		//Return all Ajax requests without layout
		$events->attach(__NAMESPACE__, 'dispatch', function($e) {
			$result = $e->getResult();
			if ($result instanceof \Zend\View\Model\ViewModel) {
				$result->setTerminal($e->getRequest()->isXmlHttpRequest());
			}
        });
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                APPLICATION_PATH . '/config/src_classmap.php',
            ),
            'Zend\Loader\ClassMapAutoloader' => array(
                APPLICATION_PATH . '/config/module_classmap.php',
            ),
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
		$action     = $matches->getParam('action');
		if (false !== strpos($controller, __NAMESPACE__)) {
			// Set the layout template
			$viewModel = $e->getViewModel();
			if (preg_match('/^layout\/layout$/',$viewModel->getTemplate())) {
				$viewModel->setTemplate('application/layout');
			}
		}
	}

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Zend\Authentication\AuthenticationService' => function($serviceManager) {
                        // If you are using DoctrineORMModule:
                        return $serviceManager->get('doctrine.authenticationservice.orm_default');
                    },
				'cache-general' => function () {
					return \Zend\Cache\StorageFactory::factory(array(
						'adapter' => array(
							'name' => 'filesystem',
							'options' => array(
								'cache_dir' => APPLICATION_PATH . '/data/cache/general',
								'ttl' => 3600
							),
						),
						'plugins' => array(
							'exception_handler' => array('throw_exceptions' => false),
							'serializer'
						)					
					));
				},
				'cache-page' => function () {
					return \Zend\Cache\StorageFactory::factory(array(
						'adapter' => array(
							'name' => 'filesystem',
							'options' => array(
								'cache_dir' => APPLICATION_PATH . '/data/cache/page',
								'ttl' => 3600
							),
						),
						'plugins' => array(
							'exception_handler' => array('throw_exceptions' => false),
							'serializer'
						)					
					));
				},
            ),
        );
    }
}
