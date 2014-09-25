<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Factory;
use Zend\Config;

class UserController extends AbstractActionController
{
    public function __construct()
    {
        $this->_view = new ViewModel();

    }

    public function indexAction()
    {
        $this->getServiceLocator()
            ->get('ViewHelperManager')
            ->get('HeadTitle')
            ->set('TownSpot &bull; Your Town. Your Talent. Spotlighted');
        return new ViewModel();
    }

    public function loginAction() {
        $factory = new Factory();
        $formElements = $this->serviceLocator->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $inputFilters = $this->serviceLocator->get('InputFilterManager');
        $factory->getInputFilterFactory()->setInputFilterManager($inputFilters);

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

        $form = $factory->createForm($formConfig);

        $this->_view->setVariable('loginForm',$form);

        return $this->_view;
    }
}