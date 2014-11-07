<?php
/**
 * Created by IntelliJ IDEA.
 * User: ian
 * Date: 9/24/14
 * Time: 10:49 PM
 */

namespace Application\Forms\Video\Upload;

use Townspot\Form\Form;

class FooterForm extends Form {
    public function __construct($name = null)
    {
        parent::__construct('uploadFooter');
        $this->setAttribute('method', 'POST');

        $this->add(array(
            'name' => 'allow_contact',
            'attributes' => array(
                'type' => 'checkbox',
                'label' => 'Yes, allow users to message me (optional)',
                'column' => 'span',
            ),
        ));
        $this->add(array(
            'name' => 'authorised',
            'attributes' => array(
                'type' => 'checkbox',
                'label' => ' Yes, I am authorized to post this video pursuant to <a href="video-submission-agreement" target="_blank">TownSpot\'s Video Submission Agreement</a>.',
                'column' => 'span',
                'errorId' => 'noauth',
                'errorMessage' => 'You must confirm that you are authorized to post this video'
            ),
        ));

    }
} 