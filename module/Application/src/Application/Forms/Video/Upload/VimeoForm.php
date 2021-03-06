<?php
/**
 * Created by IntelliJ IDEA.
 * User: ian
 * Date: 9/24/14
 * Time: 10:49 PM
 */

namespace Application\Forms\Video\Upload;

use Townspot\Form\Form;

class VimeoForm extends Form {
    public function __construct($name = null)
    {
        parent::__construct('VimeoForm');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'vimeo_url',
            'attributes' => array(
                'type'  => 'text',
                'label' => false,
                'placeholder' => 'Enter Vimeo URL',
                'errorId' => 'novimeo',
                'errorMessage' => 'You must enter a Vimeo Video'
            ),
        ));
    }
}