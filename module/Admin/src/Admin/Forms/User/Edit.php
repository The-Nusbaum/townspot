<?php
namespace Admin\Forms\User;

use Townspot\Form\Form;

class Edit extends Form
{
    protected $_countries;
    protected $_provinces;
    protected $_cities;

    public function __construct($name = null, $colCountries = null, $colProvinces = null, $colCities = null)
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
                'errorId' => 'nousername',
                'errorMessage' => 'You must enter a username'
            ),
        ));
		
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'role_id',
            'attributes' => array(
                'type'  => 'select',
                'label' => 'Role',
                'placeholder' => 'Please select a Role',
                'width' => 6,
                'errorId' => 'norole',
                'errorMessage' => 'You must select a role'
            ),
            'options' => array(
                'value_options' => array(
					'User'			=> 'User',
					'Artist'		=> 'Artist',
					'Administrator'	=> 'Administrator',
				)
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
                'errorId' => 'noemail',
                'errorMessage' => 'You must enter an email'
            ),
        ));
        $this->add(array(
            'name' => 'firstName',
            'attributes' => array(
                'type'  => 'text',
                'label' => 'First Name',
                'length' => '50',
                'width' => 6,
                'errorId' => 'nofirstname',
                'errorMessage' => 'You must enter a first name'
            ),
        ));
        $this->add(array(
            'name' => 'lastName',
            'attributes' => array(
                'type'  => 'text',
                'label' => 'Last Name',
                'length' => '50',
                'width' => 6,
                'errorId' => 'nolastname',
                'errorMessage' => 'You must enter a last name'
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
            'name' => 'country_id',
            'attributes' => array(
                'type'  => 'hidden',
                'value' => '99',
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
                'errorId' => 'nostate',
                'errorMessage' => 'You must select a state'
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
                'errorId' => 'nocity',
                'errorMessage' => 'You must select a city'
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
                'value' => 'http://images.townspot.tv/resizer.php?id=none&type=profile',
                'errorId' => 'noimage',
                'errorMessage' => 'You must upload an image'
            ),
        ));

        $this->add(array(
            'name' => 'allow_contact',
            'attributes' => array(
                'type'  => 'checkbox',
                'label' => 'Allow other users to contact',
                'column' => 'span',
                'width' => 6,
            ),
        ));

        $this->add(array(
            'name' => 'email_notifications',
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