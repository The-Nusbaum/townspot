<?php
namespace Admin\Forms;

use Townspot\Form\Form;

class Series extends Form
{
    protected $_users;

    public function __construct($name = null, $colUsers = null)
    {
        // we want to ignore the name passed
        parent::__construct('series');

        foreach($colUsers as $u) {
            $users[$u->getId()] = $u->getUsername();
        }

        $this->setAttribute('method', 'post');
        $this->setAttribute('id', 'series_edit');

        $this->add(array(
            'name' => 'series_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
		
        $this->add(array(
            'name' => 'selected_media',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'user_id',
            'attributes' => array(
                'type'  => 'select',
                'label' => 'User',
                'placeholder' => 'Please select a User',
                'width' => 6,
                'errorId' => 'norole',
                'errorMessage' => 'You must select a user'
            ),
            'options' => array(
                'value_options' => $users
            ),
        ));

        $this->add(array(
            'name' => 'seriesname',
            'attributes' => array(
                'type'  => 'text',
                'label' => 'Name',
                'length' => '50',
                'errorId' => 'noseriesname',
                'errorMessage' => 'You must enter a series name'
            ),
        ));
		
    }
}