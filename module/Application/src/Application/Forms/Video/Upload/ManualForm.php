<?php
/**
 * Created by IntelliJ IDEA.
 * User: ian
 * Date: 9/24/14
 * Time: 10:49 PM
 */

namespace Application\Forms\Video\Upload;

use Townspot\Form\Form;

class ManualForm extends Form {
    public function __construct($name = null, $colCategories = array())
    {
        parent::__construct('ManualForm');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'well');
        $this->setAttribute('columns',2);

        $categories = array();
        foreach($colCategories as $c) {
            $categories[$c->getId()] = $c->getName();
        }

        $this->add(array(
            'name' => 'title',
            'attributes' => array(
                'type'  => 'text',
                'label' => false,
                'placeholder' => 'Title (required)',
                'column' => 1,
                'errorId' => 'notitle',
                'errorMessage' => 'You must choose a title'
            ),
        ));

        $this->add(array(
            'name' => 'logline',
            'attributes' => array(
                'type'  => 'text',
                'label' => false,
                'placeholder' => 'Logline (required)',
                'column' => 2,
                'errorId' => 'nologline',
                'errorMessage' => 'You must choose a logline'
            ),
        ));

        $this->add(array(
            'name' => 'description',
            'attributes' => array(
                'type'  => 'textarea',
                'label' => 'Description (required)',
                'id' => 'description',
                'column' => 'span',
                'errorId' => 'nodescription',
                'errorMessage' => 'You must choose a description'
            ),
        ));

        $this->add(array(
            'name' => 'selections',
            'attributes' => array(
                'type'  => 'custom-block',
                'label' => 'Selections (required):',
                'inner-html' => '<span class="selectedCategories"></span>',
                'column' => 'span',
                'errorId' => 'nocategories',
                'errorMessage' => 'You must choose at least one category'
            ),
        ));
        $this->add(array(
            'name' => 'categories',
            'attributes' => array(
                'type'  => 'tree',
                'label' => false,
                'column' => 'span',
                'tree-columns' => array(
                    'Categories' => array(
                        //'values' => $categories,
                        'parents' => true,
                        'class' => 'categories',
                        'item-class' => 'category'
                    ),
                    'Genres' => array(),
                    'Sub-Genres' => array()
                )
            ),
        ));
    }
}