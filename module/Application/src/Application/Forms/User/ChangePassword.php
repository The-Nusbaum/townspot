<?php
namespace Application\Forms\User;

use Townspot\Form\Form;

class ChangePassword extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('change password');

        $this->setAttribute('id','changePassword');
        $this->setAttribute('action','/change-password');

        $this->setAttribute('method', 'post');
        $this->setAttribute('columns',2);

        $this->add(array(
            'name' => 'user_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'password_requirements',
            'attributes' => array(
                'type' => 'custom-block',
                'class' => 'well',
                'label' => 'Password Requirements',
                'inner-html' => "<ul class='list-unstyled'>
				                <li>One uppercase Letter</li>
				                <li>One lowercase Letter</li>
				                <li>One number</li>
				                <li>at least 8 characters</li>
				                <li>no more than 15 characters</li>
				            </ul>",
                'errorId' => 'invalidpass',
                'errorMessage' => 'You must enter a valid password',
                'column' => 2
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
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'button',
                'btn-type' => 'submit',
                'label' => 'Change Password',
                'column' => '1',
            ),
        ));

        foreach($this->getElements() as $e){
            if(!$e->getAttribute('column')) $e->setAttribute('column',1);
        }
    }
}