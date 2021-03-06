<?php

namespace DoctrineORMModule\Proxy\__CG__\Townspot\City;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Entity extends \Townspot\City\Entity implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = array();



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return array('__isInitialized__', '_id', '_name', '_timezone', '_latitude', '_longitude', '_coords', '_created', '_updated', '_neighborhoods', '_country', '_country_region', '_province', '_users', '_media');
        }

        return array('__isInitialized__', '_id', '_name', '_timezone', '_latitude', '_longitude', '_coords', '_created', '_updated', '_neighborhoods', '_country', '_country_region', '_province', '_users', '_media');
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Entity $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', array());
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', array());
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function setName($value)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setName', array($value));

        return parent::setName($value);
    }

    /**
     * {@inheritDoc}
     */
    public function setTimezone($value)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTimezone', array($value));

        return parent::setTimezone($value);
    }

    /**
     * {@inheritDoc}
     */
    public function setLatitude($value)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLatitude', array($value));

        return parent::setLatitude($value);
    }

    /**
     * {@inheritDoc}
     */
    public function setLongitude($value)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLongitude', array($value));

        return parent::setLongitude($value);
    }

    /**
     * {@inheritDoc}
     */
    public function setCoords($value)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCoords', array($value));

        return parent::setCoords($value);
    }

    /**
     * {@inheritDoc}
     */
    public function setCreated(\DateTime $value)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCreated', array($value));

        return parent::setCreated($value);
    }

    /**
     * {@inheritDoc}
     */
    public function setUpdated(\DateTime $value)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setUpdated', array($value));

        return parent::setUpdated($value);
    }

    /**
     * {@inheritDoc}
     */
    public function addNeighborhood(\Townspot\Neighborhood\Entity $value)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addNeighborhood', array($value));

        return parent::addNeighborhood($value);
    }

    /**
     * {@inheritDoc}
     */
    public function removeNeighborhood($key)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeNeighborhood', array($key));

        return parent::removeNeighborhood($key);
    }

    /**
     * {@inheritDoc}
     */
    public function setCountry(\Townspot\Country\Entity $value)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCountry', array($value));

        return parent::setCountry($value);
    }

    /**
     * {@inheritDoc}
     */
    public function setCountryRegion(\Townspot\CountryRegion\Entity $value)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCountryRegion', array($value));

        return parent::setCountryRegion($value);
    }

    /**
     * {@inheritDoc}
     */
    public function setProvince(\Townspot\Province\Entity $value)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setProvince', array($value));

        return parent::setProvince($value);
    }

    /**
     * {@inheritDoc}
     */
    public function addUser(\Townspot\User\Entity $value)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addUser', array($value));

        return parent::addUser($value);
    }

    /**
     * {@inheritDoc}
     */
    public function removeUser($key)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeUser', array($key));

        return parent::removeUser($key);
    }

    /**
     * {@inheritDoc}
     */
    public function addMedia(\Townspot\Media\Entity $value)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addMedia', array($value));

        return parent::addMedia($value);
    }

    /**
     * {@inheritDoc}
     */
    public function removeMedia($key)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeMedia', array($key));

        return parent::removeMedia($key);
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', array());

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getName', array());

        return parent::getName();
    }

    /**
     * {@inheritDoc}
     */
    public function getTimezone()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTimezone', array());

        return parent::getTimezone();
    }

    /**
     * {@inheritDoc}
     */
    public function getLatitude()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLatitude', array());

        return parent::getLatitude();
    }

    /**
     * {@inheritDoc}
     */
    public function getLongitude()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLongitude', array());

        return parent::getLongitude();
    }

    /**
     * {@inheritDoc}
     */
    public function getCoords()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCoords', array());

        return parent::getCoords();
    }

    /**
     * {@inheritDoc}
     */
    public function getCreated()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCreated', array());

        return parent::getCreated();
    }

    /**
     * {@inheritDoc}
     */
    public function getUpdated()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUpdated', array());

        return parent::getUpdated();
    }

    /**
     * {@inheritDoc}
     */
    public function getNeighborhoods()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getNeighborhoods', array());

        return parent::getNeighborhoods();
    }

    /**
     * {@inheritDoc}
     */
    public function getCountry()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCountry', array());

        return parent::getCountry();
    }

    /**
     * {@inheritDoc}
     */
    public function getCountryRegion()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCountryRegion', array());

        return parent::getCountryRegion();
    }

    /**
     * {@inheritDoc}
     */
    public function getProvince()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getProvince', array());

        return parent::getProvince();
    }

    /**
     * {@inheritDoc}
     */
    public function getUsers()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUsers', array());

        return parent::getUsers();
    }

    /**
     * {@inheritDoc}
     */
    public function getMedia()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getMedia', array());

        return parent::getMedia();
    }

    /**
     * {@inheritDoc}
     */
    public function getFullName()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getFullName', array());

        return parent::getFullName();
    }

    /**
     * {@inheritDoc}
     */
    public function getDiscoverLink()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDiscoverLink', array());

        return parent::getDiscoverLink();
    }

    /**
     * {@inheritDoc}
     */
    public function getRandomMedia()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRandomMedia', array());

        return parent::getRandomMedia();
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'toArray', array());

        return parent::toArray();
    }

}
