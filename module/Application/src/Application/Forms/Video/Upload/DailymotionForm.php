<?php
/**
 * Created by IntelliJ IDEA.
 * User: ian
 * Date: 9/24/14
 * Time: 10:49 PM
 */

namespace Application\Forms\Video\Upload;

use Townspot\Form\Form;

class DailymotionForm extends Form {
    public function __construct($name = null)
    {
        parent::__construct('dmForm');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'dm_url',
            'attributes' => array(
                'type'  => 'text',
                'label' => false,
                'placeholder' => 'Enter Daily Motion URL',
                'errorId' => 'nodm',
                'errorMessage' => 'You must enter a Daily Motion Video'
            ),
        ));
    }
}