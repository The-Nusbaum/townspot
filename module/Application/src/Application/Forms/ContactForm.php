<?php
namespace Application\Forms;

use Zend\Form\Element;
use Zend\Form\Form;

class ContactForm extends Form
{
    public function __construct()
    {
		parent::__construct('contact');
        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'name',
            'options' => array(
                'label' => 'Name',
            ),
            'attributes' => array(
                'required' 	=> 'required',
				'class'		=> 'form-control'
            ),
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Email',
            'name' => 'email',
            'options' => array(
                'label' => 'E-mail',
            ),
            'attributes' => array(
                'required' => 'required',
				'class'		=> 'form-control'
            ),
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'subject',
            'options' => array(
                'label' => 'Subject',
            ),
            'attributes' => array(
                'required' => 'required',
				'class'		=> 'form-control'
            ),
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Textarea',
            'name' => 'message',
            'options' => array(
                'label' => 'Message',
            ),
            'attributes' => array(
				'class'		=> 'form-control',
                'required'  => 'required',
                'rows' 		=> '6',
                'cols' 		=> '100',
            ),
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Submit',
            'name' => 'send',
            'attributes' => array(
                'value' => 'Send',
				'class' => 'btn btn-large btn-primary',
            ),
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Button',
            'name' => 'reset',
            'options' => array(
                'label' => 'Clear Form',
            ),
            'attributes' => array(
				'type'  => 'reset',
				'class' => 'btn btn-large',
            ),
        ));
    }
}
/*
    public function prepareElements()
    {
        $this->add(array(
            'name' => 'name',
            'options' => array(
                'label' => 'Name',
            ),
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Email',
            'name' => 'email',
            'options' => array(
                'label' => 'Your email address',
            ),
        ));
        $this->add(array(
            'name' => 'subject',
            'options' => array(
                'label' => 'Subject',
            ),
            'attributes' => array(
                'type'  => 'text',
            ),
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Textarea',
            'name' => 'message',
            'options' => array(
                'label' => 'Message',
            ),
        ));
        $this->add(array(
            'name' => 'send',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Submit',
            ),
        ));
    }
}
*/