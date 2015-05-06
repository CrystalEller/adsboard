<?php

return array(
    'router' => array(
        'routes' => array(
            'getForm' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/getForm/:catid',
                    'constraints' => array(
                        'catid' => '\d+'
                    ),
                    'defaults' => array(
                        'controller' => 'FormBuilder\Controller\FormBuilder',
                        'action' => 'getForm',
                    )
                )
            ),
            'changeForm' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/changeForm/:catid',
                    'constraints' => array(
                        'catid' => '\d+'
                    ),
                    'defaults' => array(
                        'controller' => 'FormBuilder\Controller\FormBuilder',
                        'action' => 'changeForm',
                    )
                )
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'FormBuilder\Controller\FormBuilder' => 'FormBuilder\Controller\FormBuilderController'
        ),
    ),
    'validators' => array(
        'factories' => array(
            'FormBuilder' => function ($sm) {

                $postData = $sm->getServiceLocator()->get('Request')->getPost();
                $em = $sm->getServiceLocator()->get('doctrine.entitymanager.orm_default');

                if (is_array($postData['category'])) {
                    foreach ($postData['category'] as $key => $val) {
                        $categoryIp = $val . '';
                    }
                } else {
                    $categoryIp = $postData['category'][0];
                }

                return new \FormBuilder\Validator\FormValidator\FormBuilder(array(
                    'attr_repository' => $em->getRepository('\Application\Entity\CategoryAttributes'),
                    'val_repository' => $em->getRepository('\Application\Entity\CategoryAttributesValues'),
                    'html_name_prefix' => 'prop',
                    'category_ip' => $categoryIp
                ));
            }
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'form-builder' => __DIR__ . '/../view/form-builder/form-builder/get-form.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
