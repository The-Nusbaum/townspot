<?php
/**
 * Created by IntelliJ IDEA.
 * User: ian
 * Date: 9/24/14
 * Time: 10:49 PM
 */

namespace Application\Form;

use Zend\Form\Form;

class LoginForm extends Form {
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('login');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'username',
            'attributes' => array(
                'type'  => 'text',
            ),
        ));
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type'  => 'password',
            ),
        ));
    }
} 