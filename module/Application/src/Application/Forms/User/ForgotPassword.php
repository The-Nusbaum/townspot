<?php
namespace Application\Forms\User;

use Townspot\Form\Form;

class ForgotPassword extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('forgot password');

        $this->setAttribute('id','forgotPassword');
        $this->setAttribute('method','POST');
        $this->setAttribute('action','/reset-sent');

        $this->setAttribute('method', 'post');
        $this->setAttribute('columns',2);

        $this->add(array(
            'name' => 'username',
            'attributes' => array(
                'type'  => 'text',
                'label' => 'Username or Email',
                'length' => '50',
                'column' => 1
            ),
        ));


        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'button',
                'btn-type' => 'submit',
                'label' => 'Send Email',
                'column' => '1',
            ),
        ));

        $this->add(array(
            'name' => 'resetInfo',
            'attributes' => array(
                'type'  => 'custom-block',
                'label' => 'Resetting your password',
                'inner-html' => "
                <p class='well'>
                    Please enter either your username or password to the left. If an account is found we will send an
                    email with instructions on resetting your password
                </p>
                ",
                'column' => '2',
            ),
        ));

        foreach($this->getElements() as $e){
            if(!$e->getAttribute('column')) $e->setAttribute('column',1);
        }
    }
}