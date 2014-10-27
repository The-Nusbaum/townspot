<?php
$formConfig = [
    'elements' => [
        [
            'spec' => [
                'name' => 'name',
                'options' => [
                    'label' => 'Name',
                ],
                'attributes' => [
                    'type' => 'text',
                    'class' => 'form-control',
                    'required' => 'required',
                ],
            ],
        ],
        [
            'spec' => [
                'name' => 'email',
                'options' => [
                    'label' => 'E-Mail',
                ],
                'attributes' => [
                    'type' => 'text',
                    'class' => 'form-control',
                    'required' => 'required',
                ],
            ],
        ],
        [
            'spec' => [
                'name' => 'subject',
                'options' => [
                    'label' => 'Subject',
                ],
                'attributes' => [
                    'type' => 'text',
                    'class' => 'form-control',
                    'required' => 'required',
                ],
            ],
        ],
        [
            'spec' => [
                'name' => 'comment',
                'options' => [
                    'label' => 'Your comment',
                ],
                'attributes' => [
                    'type' => 'textarea',
                    'class' => 'form-control',
                    'required' => 'required',
                ],
            ],
        ],
    ],
    'input_filter' => [
        'name' => [
            'name'       => 'name',
            'required'   => true,
            'validators' => [
                [
                    'name' => 'not_empty',
                ],
                [
                    'name' => 'string_length',
                    'options' => [
                        'max' => 30,
                    ],
                ],
            ],
        ],
        'email' => [
            'name'       => 'email',
            'required'   => true,
            'validators' => [
                [
                    'name' => 'not_empty',
                ],
                [
                    'name' => 'email_address',
                ],
            ],
        ],
        'subject' => [
            'name'       => 'subject',
            'required'   => true,
            'validators' => [
                [
                    'name' => 'not_empty',
                ],
                [
                    'name' => 'string_length',
                    'options' => [
                        'max' => 60,
                    ],
                ],
            ],
        ],
        'comment' => [
            'name'       => 'comment',
            'required'   => true,
            'validators' => [
                [
                    'name' => 'not_empty',
                ],
            ],
        ],
    ],
];