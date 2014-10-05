<?php
namespace Application\Forms\User;

use Townspot\Form\Form;

class Edit extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('user');
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
            'name' => 'artist_name',
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
            'name' => 'first_name',
            'attributes' => array(
                'type'  => 'text',
                'label' => 'First Name',
                'length' => '50',
                'width' => 6,
            ),
        ));
        $this->add(array(
            'name' => 'last_name',
            'attributes' => array(
                'type'  => 'text',
                'label' => 'Last Name',
                'length' => '50',
                'width' => 6,
            ),
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
            'name' => 'display_name',
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
                'placeholder' => 'Please select a Country'
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
        ));
        $this->add(array(
            'name' => 'neighborhood',
            'attributes' => array(
                'type'  => 'text',
                'label' => 'neighborhood'
            ),
        ));
        $this->add(array(
            'name' => 'about_me',
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
            'name' => 'image_url',
            'attributes' => array(
                'type'  => 'plupload-image',
                'column' => 2,
                'label' => 'Please click the image to choose a profile Picture',
                'class' => 'profilePic',
                'value' => 'http://images.townspot.tv/resizer.php?id=none&type=profile'
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
            'name' => 'allow_contact',
            'attributes' => array(
                'type'  => 'checkbox',
                'label' => 'Allow other users to contact me',
                'column' => 'span',
                'width' => 4,
            ),
        ));
        $this->add(array(
            'name' => 'terms_agreement',
            'attributes' => array(
                'type'  => 'checkbox',
                'label' => 'Receive email notifications',
                'column' => 'span',
                'width' => 4,
            ),
        ));
        $this->add(array(
            'name' => 'email_notifications',
            'attributes' => array(
                'type'  => 'checkbox',
                'label' => 'Receive Email Notifications',
                'column' => 'span',
                'width' => 4,
            ),
        ));

        foreach($this->getElements() as $e){
            if(!$e->getAttribute('column')) $e->setAttribute('column',1);
        }
    }
}