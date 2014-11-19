<?php
/**
 * Created by IntelliJ IDEA.
 * User: ian
 * Date: 9/24/14
 * Time: 10:49 PM
 */

namespace Application\Forms\Video\Upload;

use Townspot\Form\Form;

class VideoMediaForm extends Form {
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('VideoMediaForm');
        $this->add(array(
            'name' => 'url',
            'attributes' => array(
                'type'  => 'plupload-video',
                'label' => '<i class="fa fa-video-camera"></i> Choose Video',
                'class' => 'vidUpload',
                'errorId' => 'novideo',
                'errorMessage' => 'You must upload a Video'
            ),
        ));

        $this->add(array(
            'name' => 'previewImage',
            'attributes' => array(
                'type'  => 'plupload-image',
                'label' => '<i class="fa fa-camera"></i> Choose Picture',
                'class' => 'picUpload',
                'errorId' => 'nopreview',
                'errorMessage' => 'You must upload an Image'

            ),
        ));
    }
} 