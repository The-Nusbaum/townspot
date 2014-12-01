<?php
/**
 * Created by IntelliJ IDEA.
 * User: ian
 * Date: 9/24/14
 * Time: 10:49 PM
 */

namespace Application\Forms\Video\Upload;

use Townspot\Form\Form;

class YtForm extends Form {
    public function __construct($name = null)
    {
        parent::__construct('YtForm');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'youtube_url',
            'attributes' => array(
                'type'  => 'text',
                'label' => false,
                'placeholder' => 'Enter YouTube URL',
                'errorId' => 'noyoutube',
                'errorMessage' => 'You must enter a Youtube Video'
            ),
        ));
    }
}