<?php
namespace Application\Forms\User;

use Townspot\Form\Form;

class Edit extends Form
{
    protected $_countries;
    protected $_provinces;
    protected $_cities;

    public function __construct($name = null,$colCountries = null, $colProvinces = null, $colCities = null)
    {
        // we want to ignore the name passed
        parent::__construct('user');

        $this->setAttribute('id','userEdit');

        foreach($colCountries as $c) {
            $countries[$c->getId()] = $c->getName();
        }

        foreach($colProvinces as $p) {
            $provinces[$p->getId()] = $p->getName();
        }

        foreach($colCities as $c) {
            $cities[$c->getId()] = $c->getName();
        }

        $this->setAttribute('method', 'post');
        $this->setAttribute('columns',2);
        $this->add(array(
            'name' => 'user_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'username',
            'attributes' => array(
                'type'  => 'text',
                'label' => 'Username',
                'length' => '50',
            ),
        ));
        $this->add(array(
            'name' => 'artistName',
            'attributes' => array(
                'type'  => 'text',
                'label' => 'Artist Name',
                'length' => '50',
            ),
        ));
        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type'  => 'email',
                'label' => 'Email',
                'length' => '50',
            ),
        ));
        $this->add(array(
            'name' => 'firstName',
            'attributes' => array(
                'type'  => 'text',
                'label' => 'First Name',
                'length' => '50',
                'width' => 6,
            ),
        ));
        $this->add(array(
            'name' => 'lastName',
            'attributes' => array(
                'type'  => 'text',
                'label' => 'Last Name',
                'length' => '50',
                'width' => 6,
            ),
        ));
        $this->add(array(
            'name' => 'password_requirements',
            'attributes' => array(
                'type' => 'custom-block',
                'label' => 'Password Requirements',
                'inner-html' => "<ul class='list-unstyled'>
				                <li>One uppercase Letter</li>
				                <li>One lowercase Letter</li>
				                <li>One number</li>
				                <li>at least 8 characters</li>
				                <li>no more than 15 characters</li>
				            </ul>",
                'errorId' => 'invalidpass',
                'errorMessage' => 'You must enter a valid password'
            )
        ));
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type'  => 'password',
                'label' => 'Password',
                'length' => '50',
                'width' => 6,
            ),
        ));
        $this->add(array(
            'name' => 'password2',
            'attributes' => array(
                'type'  => 'password',
                'label' => 'Confirm Password',
                'length' => '50',
                'width' => 6,
            ),
        ));
        $this->add(array(
            'name' => 'displayName',
            'attributes' => array(
                'type'  => 'text',
                'label' => 'Display Name',
                'length' => '50',
            ),
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'country_id',
            'attributes' => array(
                'type'  => 'select',
                'label' => 'Country',
                'placeholder' => 'Please select a Country',
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
                'width' => 6,
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
            ),
            'options' => array(
                'value_options' => $cities
            ),
        ));
        $this->add(array(
            'name' => 'neighborhood',
            'attributes' => array(
                'type'  => 'text',
                'label' => 'neighborhood'
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
            'name' => 'website',
            'attributes' => array(
                'type'  => 'text',
                'label' => 'Website',
                'length' => '50',
            ),
        ));
        $this->add(array(
            'name' => 'imageUrl',
            'attributes' => array(
                'type'  => 'plupload-image',
                'column' => 2,
                'label' => 'Please choose a profile Picture',
                'class' => 'profilePic',
            ),
        ));
        $this->add(array(
            'name' => 'link_twitter',
            'attributes' => array(
                'type'  => 'button',
                'column' => 2,
                'width' => 6,
                'label' => 'Link Twitter',
                'button-class' => 'primary',
            ),
        ));
        $this->add(array(
            'name' => 'link_facebook',
            'attributes' => array(
                'type'  => 'button',
                'column' => 2,
                'width' => 6,
                'label' => 'Link Facebook',
                'button-class' => 'primary',
            ),
        ));
        $this->add(array(
            'name' => 'allowContact',
            'attributes' => array(
                'type'  => 'checkbox',
                'label' => 'Allow other users to contact me',
                'column' => 'span',
                'width' => 6,
            ),
        ));

        $this->add(array(
            'name' => 'emailNotification',
            'attributes' => array(
                'type'  => 'checkbox',
                'label' => 'Receive Email Notifications',
                'column' => 'span',
                'width' => 6,
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