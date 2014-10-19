<?php
namespace Townspot\Doctrine\Mapper;

use Townspot\Doctrine\Mapper\EntityMapperInterface;
use Townspot\Doctrine\Entity\Container;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\DBAL\LockMode;
	
abstract class AbstractEntityMapper implements EntityMapperInterface, ServiceLocatorAwareInterface
{
    /**
     * @var Service
     */
    protected $_service;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $_entityManager;

    /**
     * @var Object
     */
    protected $_entity;

    /**
     * @var string
     */
    protected $_repositoryName;

    /**
     * @var Object
     */
    protected $_repository;

    /**
     * Repository
     *
     * @param Object $entity
     * @return AbstractEntityMapper
     */
    public function __construct(ServiceLocatorInterface $serviceLocator) 
	{
		$this->setServiceLocator($serviceLocator);
	}

    /**
     * Repository
     *
     * @param Object $entity
     * @return AbstractEntityMapper
     */
    public function setEntity($entity)
    {
        $this->_entity = $entity;
        return $this;
    }

    /**
     * Gets the value of Entity
     *
     * @return Object
     */
    public function getEntity()
    {
        if ( ! isset($this->_entity)) {
            $entity = $this->getRepositoryName();
            $this->setEntity(new $entity);
        }
        return $this->_entity;
    }

    /**
     * Find an entity by an id
     *
     * @param mixed    $id          The identifier.
     * @param int      $lockMode    The lock mode.
     * @param int|null $lockVersion The lock version.
     * @return Entity
     */
    public function find($id, $lockMode = LockMode::NONE, $lockVersion = null)
    {
        return $this->getRepository()->find($id,$lockMode,$lockVersion);
    }

	/**
	 * Find All
	 *
	 * @return array
	 */
	public function findAll()
	{
		return $this->getRepository()->findAll();
	}
	
    /**
     * Finds entities by a set of criteria.
     *
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return array The objects.
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
		return $this->getRepository()->findBy($criteria,$orderBy,$limit,$offset);
    }
	
    /**
     * Finds a single entity by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     *
     * @return object|null The entity instance or NULL if the entity can not be found.
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
		return $this->getRepository()->findOneBy($criteria,$orderBy);
    }
	
    /**
     * Adds support for magic finders.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return array|object The found entity/entities.
     *
     * @throws ORMException
     * @throws \BadMethodCallException If the method called is an invalid find* method
     *                                 or no find* method at all and therefore an invalid
     *                                 method call.
     */
    public function __call($method, $arguments)
    {
        switch (true) {
            case (0 === strpos($method, 'findBy')):
                $by = substr($method, 6);
                $method = 'findBy';
                break;

            case (0 === strpos($method, 'findOneBy')):
                $by = substr($method, 9);
                $method = 'findOneBy';
                break;

            default:
                throw new \BadMethodCallException(
                    "Undefined method '$method'. The method name must start with ".
                    "either findBy or findOneBy!"
                );
        }
        if (empty($arguments)) {
            throw new \BadMethodCallException(
                $method . $by . " requires Arguments"
             );
        }
		$reflect = new \ReflectionClass($this->getEntity());
		$_properties = $reflect->getProperties();
		$properties = array();
		foreach ($_properties as $property) {
			$properties[] = $property->getName();
		}
        $stdCase = lcfirst(\Doctrine\Common\Util\Inflector::classify($by));
		$camelCase = $this->_convertCase($by);
		$_camelCase = '_' . $camelCase;
		
		if (in_array($stdCase,$properties)) {
			$fieldName = $stdCase;
		} elseif (in_array($camelCase,$properties)) {
			$fieldName = $camelCase;
		} elseif (in_array($_camelCase,$properties)) {
			$fieldName = $_camelCase;
		} else {
			$fieldName = null;
		}
		
		if ($fieldName) {
            switch (count($arguments)) {
                case 1:
                    return $this->$method(array($fieldName => $arguments[0]));
                case 2:
                    return $this->$method(array($fieldName => $arguments[0]), $arguments[1]);
                case 3:
                    return $this->$method(array($fieldName => $arguments[0]), $arguments[1], $arguments[2]);
                case 4:
                    return $this->$method(array($fieldName => $arguments[0]), $arguments[1], $arguments[2], $arguments[3]);
                default:
                    // Do nothing
            }
        }
    }
	
    /**
     * Save an entity
     *
     * @return void
     */
    public function save()
    {
        $em = $this->getEntityManager();
        $entity = $this->getEntity();
        $em->persist($entity);
        $em->flush($entity);
    }

    /**
     * Delete an entity
     *
     * @return void
     */
     public function delete()
    {
        $em = $this->getEntityManager();
        $entity = $this->getEntity();
        $em->remove($entity);
        $em->flush($entity);
    }

    /**
     * Set Entity Manager
     *
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @return void
     */
    public function setEntityManager(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->_entityManager = $entityManager;
        return $this;
    }

    /**
     * Set Entity Manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (!isset($this->_entityManager)) {
            $entityManager = Container::getInstance($this->getServiceLocator())->getEntityManager();
            $this->setEntityManager($entityManager);
        }
        return $this->_entityManager;
    }

    /**
     * Sets the repository for the mapper to use
     *
     * @param \Doctrine\ORM\EntityRepository
     * @return AbstractEntityMapper
     */
    public function setRepository(\Doctrine\ORM\EntityRepository $repository)
    {
        $this->_repository = $repository;
        return $this;
    }

    /**
     * Gets the repository for the mapper
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        if ( ! isset($this->_repository)) {
            $this->_repository = $this->getEntityManager()->getRepository($this->getRepositoryName());
        }
        return $this->_repository;
    }

    /**
     * Sets the value of RepositoryName
     *
     * @param string 
     * @return AbstractEntityMapper
     */
    public function setRepositoryName($repositoryName)
    {
        $this->_repositoryName = $repositoryName;
        return $this;
    }

    /**
     * Gets the value of RepositoryName
     *
     * @return string
     */
    public function getRepositoryName()
    {
        return $this->_repositoryName;
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

	protected function _convertCase($input) 
	{
		preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
		$ret = $matches[0];
		foreach ($ret as &$match) {
			$match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
		}
		return implode('_', $ret);
	}	
}
