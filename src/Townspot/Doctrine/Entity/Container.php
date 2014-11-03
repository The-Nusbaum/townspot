<?php
namespace Townspot\Doctrine\Entity;

use Zend\ServiceManager\ServiceLocatorAwareInterface,
    Zend\ServiceManager\ServiceLocatorInterface;
	
class Container implements ServiceLocatorAwareInterface
{
	/**
	 * @var Container
	 */
	protected static $_instance;
	
    /**
     * @var options
     */
    protected $_serviceLocator;

	/**
	 * @return boolean
	 */
	protected $_isDebug; 
	
	/**
	 * @var array
	 */
	protected $_entityManagers = array();

	/**
	 * Singleton to return an instance
	 *
	 * @return Container
	 */
	public static function getInstance(ServiceLocatorInterface $serviceLocator)
	{
		if (!isset(static::$_instance)) {
			$class = get_class();
			static::$_instance = new $class($serviceLocator);	
		}	
		return static::$_instance;
	}

	/**
	 * Ensure singleton only
	 */
	protected function __construct($serviceLocator) {
		$this->setServiceLocator($serviceLocator);
	}

	/**
	 * Gets the entity manager for the given config
	 *
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getEntityManager($namespace = 'orm_default')
	{
		if (!isset($this->_entityManagers[$namespace])) {
			$config = $this->getServiceLocator()->get('config');
			$config = $config['doctrine'];
			$doctrineConfig = \Doctrine\ORM\Tools\Setup::createXMLMetadataConfiguration(
				array(realpath($config['entityConfigPath'])), 
				$this->getDebug()
			);
			// Setup cache class
			$cacheClass = $config['cacheClass'];
			$cache = new $cacheClass;
			$doctrineConfig->setMetadataCacheImpl($cache);
			$doctrineConfig->setQueryCacheImpl($cache);
			$proxyConfig = $config['proxy'];
			$doctrineConfig->setProxyDir(realpath($proxyConfig['path']));
			$doctrineConfig->setProxyNamespace($proxyConfig['namespace']);
			$doctrineConfig->setAutoGenerateProxyClasses($proxyConfig['enableAutoGenerate']);
			// Setup proxy info
			$connection = $config['connection'];
			$this->_entityManagers[$namespace] = \Doctrine\ORM\EntityManager::create($connection[$namespace]['params'], $doctrineConfig);
		}
		return $this->_entityManagers[$namespace];
	}

	/**
	 * Sets the value of isDebug
	 *
	 * @param boolean isDebug
	 * @return Container
	 */
	public function setDebug($isDebug)
	{
		$this->_isDebug = $isDebug;
		return $this;
	}

	/**
	 * Gets the value of isDebug
	 *
	 * @return boolean
	 */
	public function getDebug()
	{
		if ( ! isset($this->_isDebug)) {
			$this->setDebug((APPLICATION_ENV == 'development') || (APPLICATION_ENV == 'test'));
		}
		return $this->_isDebug;
	}
	
	/**
	 * Set Service Locator instance
	 *
	 * @param ServiceLocator $locator
	 * @return Object
	 */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->_serviceLocator = $serviceLocator;
        return $this;
    }
	
	/**
	 * Gets the value of isDebug
	 *
	 * @return boolean
	 */
	public function getServiceLocator()
	{
		return $this->_serviceLocator;
	}
}
