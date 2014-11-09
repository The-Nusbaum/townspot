<?php
$formConfig = [
    'elements' => [
        [
            'spec' => [
                'name' => 'name',
                'options' => [
                    'label' => 'Your name',
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
                    'label' => 'Your email address',
                ],
                'attributes' => [
                    'type' => 'text',
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
    ],
];