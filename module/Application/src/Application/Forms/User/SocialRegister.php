<?php
namespace Application\Forms\User;

use Townspot\Form\Form;

class SocialRegister extends Form
{
    protected $_countries;
    protected $_provinces;
    protected $_cities;

    public function __construct($name = null, $provider = null, $colCountries = null, $colProvinces = null, $colCities = null)
    {
        // we want to ignore the name passed
        parent::__construct('user');

        $this->setAttribute('id','socialRegister');

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

        $this->setAttribute('method', 'post');

        $emailParams = array(
            'type'  => 'hidden',
        );
        $topWidth = 6;
        if($provider == 'twitter') {
            $topWidth = 4;
            $emailParams = array(
                'type'  => 'text',
                'label' => 'Email',
                'length' => '50',
                'width' => $topWidth,
                'errorId' => 'noemail',
                'errorMessage' => 'You must enter an email',
            );
        }

        $this->add(array(
            'name' => 'external_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'provider',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'first_name',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'last_name',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'displayName',
            'attributes' => array(
                'type'  => 'text',
                'label' => 'Display Name',
                'length' => '50',
                'width' => $topWidth,
                'errorId' => 'noname',
                'errorMessage' => 'You must enter a display name',
            ),
        ));
        $this->add(array(
            'name' => 'website',
            'attributes' => array(
                'type'  => 'text',
                'label' => 'Website',
                'length' => '50',
                'width' => $topWidth
            ),
        ));
        $this->add(array(
            'name' => 'email',
            'attributes' => $emailParams,
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'country_id',
            'attributes' => array(
                'type'  => 'select',
                'label' => 'Country',
                'placeholder' => 'Please select a Country',
                'width' => '4',
                'errorId' => 'nocountry',
                'errorMessage' => 'You must select a country',
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
                'errorId' => 'nostate',
                'errorMessage' => 'You must select a state',
                'width' => '4'
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
                'errorId' => 'nocity',
                'errorMessage' => 'You must select a city',
                'width' => '4'
            ),
            'options' => array(
                'value_options' => $cities
            ),
        ));

        $this->add(array(
            'name' => 'aboutMe',
            'attributes' => array(
                'type'  => 'textarea',
                'label' => 'About Me',
                'column' => 'span'

            ),
        ));
        $this->add(array(
            'name' => 'interests',
            'attributes' => array(
                'type'  => 'textarea',
                'label' => 'Interests',
                'column' => 'span'
            ),
        ));
        $this->add(array(
            'name' => 'description',
            'attributes' => array(
                'type'  => 'textarea',
                'label' => 'Description',
                'column' => 'span'
            ),
        ));
        $this->add(array(
            'name' => 'imageUrl',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'allowContact',
            'attributes' => array(
                'type'  => 'checkbox',
                'label' => 'Allow other users to contact me',
                'column' => 'span',
                'width' => 4,
            ),
        ));
        $this->add(array(
            'name' => 'allowHire',
            'attributes' => array(
                'type'  => 'checkbox',
                'label' => 'Allow users to hire me',
                'column' => 'span',
                'width' => 4,
            ),
        ));
        $this->add(array(
            'name' => 'emailNotifications',
            'attributes' => array(
                'type'  => 'checkbox',
                'label' => 'Receive Email Notifications',
                'column' => 'span',
                'width' => 4,
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'button',
                'btn-type' => 'submit',
                'label' => 'Save Changes',
                'column' => 'span',
            ),
        ));

        foreach($this->getElements() as $e){
            if(!$e->getAttribute('column')) $e->setAttribute('column',1);
        }
    }
}