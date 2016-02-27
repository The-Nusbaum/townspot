<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Townspot\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Location extends AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $_multi = false;
    protected $_name = "%name%";

    public function __invoke($multi = false)
    {
        $helperPluginManager = $this->getServiceLocator();
        $serviceManager = $helperPluginManager->getServiceLocator();
        $this->countryMapper = new \Townspot\Country\Mapper($serviceManager);
        $this->provinceMapper = new \Townspot\Province\Mapper($serviceManager);
        $this->cityMapper = new \Townspot\Province\Mapper($serviceManager);
        $this->userMapper = new \Townspot\User\Mapper($serviceManager);
        $this->multi = $multi;

        return $this;
    }

    public function setMulti($val)
    {
        $this->_multi = $val;
    }

    public function setName($val)
    {
        $this->_name = $val;
    }

    public function countries($forUser = false)
    {
        $countries = $this->countryMapper->findBy(
            array(),
            array('_name' => 'ASC')
        );
        if ($forUser) {
            $user = $this->userMapper->find($forUser);
        }

        if ($this->_multi) {
            $id = '';
            $class = " class='country_id";
        } else {
            $id = " id='country_id'";
            $class = " class='";
        }

        $name = str_replace('%name%', 'country_id', $this->_name);

        $html = "<label for='country_id'>Country</label>";
        $html .= "<select name='$name'$id $class form-control'>";
        foreach ($countries as $c) {
            $selected = '';
            if ($forUser && $user->getCountry()->getId() == $c->getId()) $selected = " selected='selected'";
            $html .= "<option value='{$c->getId()}'$selected>{$c->getName()}</option>";
        }
        $html .= "</select>";

        return $html;
    }

    public function provinces($country, $forUser = false)
    {
        $country = $this->countryMapper->find($country);
        $provinces = $country->getProvinces();
        if ($forUser) {
            $user = $this->userMapper->find($forUser);
        }

        $name = str_replace('%name%', 'province_id', $this->_name);

        if ($this->_multi) {
            $id = '';
            $class = " class='province_id";
        } else {
            $id = " id='province_id'";
            $class = " class='";
        }

        $html = "<label for='Province_id'>State/Province</label>";
        $html .= "<select name='$name'$id $class form-control'>";
        foreach ($provinces as $p) {
            $selected = '';
            if ($forUser && $user->getProvince()->getId() == $p->getId()) $selected = " selected='selected'";
            $html .= "<option value='{$p->getId()}'$selected>{$p->getName()}</option>";
        }
        $html .= "</select>";
        return $html;
    }

    public function cities($province, $forUser = false)
    {
        $province = $this->provinceMapper->find($province);
        $cities = $province->getCities();
        if ($forUser) {
            $user = $this->userMapper->find($forUser);
        }

        if ($this->_multi) {
            $id = '';
            $class = " class='city_id";
        } else {
            $id = " id='city_id'";
            $class = " class='";
        }

        $name = str_replace('%name%','city_id', $this->_name);

        $html = "<label for='city_id'>City</label>";
        $html .= "<select name='$name'$id $class form-control'>";
        foreach ($cities as $c) {
            $selected = '';
            if ($forUser && $user->getCity()->getId() == $c->getId()) $selected = " selected='selected'";
            $html .= "<option value='{$c->getId()}'$selected>{$c->getName()}</option>";
        }
        $html .= "</select>";
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