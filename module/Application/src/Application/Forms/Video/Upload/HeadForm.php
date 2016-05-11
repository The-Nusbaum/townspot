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
        asort($countries);

        foreach($colProvinces as $p) {
            $provinces[$p->getId()] = $p->getName();
        }
        asort($provinces);

        foreach($colCities as $c) {
            $cities[$c->getId()] = $c->getName();
        }
        asort($cities);

        $this->add(array(
            'name' => 'user_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'country_id',
            'attributes' => array(
                'type'  => 'select',
                'label' => 'Country',
                'placeholder' => 'Please select a Country',
                'errorId' => 'nocountry',
                'errorMessage' => 'You must choose a country',
                'width' => 4
            ),
            'options' => array(
                'value_options' => $countries
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'province_id',
            'attributes' => array(
                'type'  => 'select',
                'label' => 'State',
                'placeholder' => 'Please select a State',
                'width' => 4,
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
                'width' => 4,
                'errorId' => 'noAuth',
                'errorMessage' => 'You must confirm that you are authorized to post this video'
            ),
            'options' => array(
                'value_options' => $cities
            ),
        ));
        $this->add(array(
            'name' => 'allow_contact',
            'attributes' => array(
                'type' => 'checkbox',
                'label' => 'Yes, allow users to message me (optional)',
                'width' => '12',
            ),
        ));
        $this->add(array(
            'name' => 'authorised',
            'attributes' => array(
                'type' => 'checkbox',
                'label' => ' Yes, I am authorized to post this/these video(s) pursuant to <a href="video-submission-agreement" target="_blank">TownSpot\'s Video Submission Agreement</a>.',
                'width' => '12',
                'errorId' => 'noauth',
                'errorMessage' => 'You must confirm that you are authorized to post this video'
            ),
        ));
    }
} 