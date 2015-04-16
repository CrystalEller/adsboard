<?php
return array(

    'router' => array(
        'routes' => array(
            'admin' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Admin',
                        'action' => 'index',
                    ),
                ),
            ),
            'showUsers' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin/users',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Admin',
                        'action' => 'users',
                    ),
                ),
            ),
            'showCategories' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/categories[/:action]',
                    'constraints' => array(
                        'action' => '[a-z]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Category',
                        'action' => 'index',
                    )
                )
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'Admin\Controller\Admin' => 'Admin\Controller\AdminController',
            'Admin\Controller\Category' => 'Admin\Controller\CategoryController'
        ),
    ),

    'service_manager' => array(
        'factories' => array(
            'Admin\Form\CategoryFilter' => function ($sm) {
                return new \Admin\Form\CategoryFilter(
                    $sm->get('doctrine.entitymanager.orm_default')
                );
            },
        )
    ),

    'view_manager' => array(
        'template_map' => array(
            'admin/layout/admin' => __DIR__ . '/../view/layout/admin.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);
