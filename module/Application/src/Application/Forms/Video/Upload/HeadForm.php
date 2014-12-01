<?php
/**
 * Created by IntelliJ IDEA.
 * User: ian
 * Date: 9/24/14
 * Time: 10:49 PM
 */

namespace Application\Forms\Video\Upload;

use Townspot\Form\Form;

class HeadForm extends Form {
    public function __construct($name = null, $colCountries = null, $colProvinces = null, $colCities = null)
    {
        parent::__construct('uploadHeader');
        $this->setAttribute('method', 'post');
        $this->setAttribute('id','headForm');

        foreach($colCountries as $c) {
            $countries[$c->getId()] = $c->getName();
        }

        foreach($colProvinces as $p) {
            $provinces[$p->getId()] = $p->getName();
        }

        foreach($colCities as $c) {
            $cities[$c->getId()] = $c->getName();
        }

        $this->add(array(
            'name' => 'user_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
/*
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'country_id',
            'attributes' => array(
                'type'  => 'select',
                'label' => 'Country',
                'placeholder' => 'Please select a Country',
                'errorId' => 'nocountry',
                'errorMessage' => 'You must choose a country'
            ),
            'options' => array(
                'value_options' => $countries
            ),
        ));
*/
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'province_id',
            'attributes' => array(
                'type'  => 'select',
                'label' => 'State',
                'placeholder' => 'Please select a State',
                'width' => 6,
                'errorId' => 'nostate',
                'errorMessage' => 'You must choose a state'
            ),
            'options' => array(
                'value_options' => $provinces
            ),
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'city_id',
            'attributes' => array(
                'type'  => 'select',
                'label' => 'City',
                'placeholder' => 'Please select a City',
                'width' => 6,
                'errorId' => 'noAuth',
                'errorMessage' => 'You must confirm that you are authorized to post this video'
            ),
            'options' => array(
                'value_options' => $cities
            ),
        ));
    }
} 