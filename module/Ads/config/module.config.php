<?php
return array(

    'router' => array(
        'routes' => array(
            'showFormAds' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/showFormAds',
                    'defaults' => array(
                        'controller' => 'Ads\Controller\Ads',
                        'action' => 'showFormAds',
                    ),
                ),
            ),
            'getCities' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/getCities',
                    'defaults' => array(
                        'controller' => 'Ads\Controller\Ads',
                        'action' => 'getCities',
                    ),
                ),
            ),
            'getCategories' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/getCategories',
                    'defaults' => array(
                        'controller' => 'Ads\Controller\Ads',
                        'action' => 'getCategories',
                    ),
                ),
            ),
            'createAds' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/createAds',
                    'defaults' => array(
                        'controller' => 'Ads\Controller\Ads',
                        'action' => 'create',
                    ),
                ),
            ),
            'mainCategories' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/category',
                    'defaults' => array(
                        'controller' => 'Ads\Controller\Ads',
                        'action' => 'mainCategories',
                    )
                ),
            ),
            'adsByCategory' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/category/:catId',
                    'constraints' => array(
                        'catId' => '\d+'
                    ),
                    'defaults' => array(
                        'controller' => 'Ads\Controller\Ads',
                        'action' => 'adsByCategory',
                    )
                ),
            ),
            'adsByAttributeValues' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/category/:catId/attribute/:attrId',
                    'constraints' => array(
                        'catId' => '\d+',
                        'attrId' => '\d+'
                    ),
                    'defaults' => array(
                        'controller' => 'Ads\Controller\Ads',
                        'action' => 'adsByAttributeValues',
                    )
                )
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'Ads\Controller\Ads' => 'Ads\Controller\AdsController',
        ),
    ),

    'service_manager' => array(
        'factories' => array(
            'Ads\Filter\AdsFilter' => function ($sm) {

                $entityManager = $sm->get('doctrine.entitymanager.orm_default');
                $config = new \DoctrineExtensions\NestedSet\Config($entityManager, 'Application\Entity\Categories');
                $nsm = new \DoctrineExtensions\NestedSet\Manager($config);

                return new \Ads\Filter\AdsFilter($entityManager, $nsm);
            },
            'Ads\Filter\PriceFilter' => function ($sm) {
                return new \Ads\Filter\PriceFilter(
                    $sm->get('doctrine.entitymanager.orm_default')
                );
            },
            'Ads\Service\AdsSelector' => function ($sm) {

                return new \Ads\Service\AdsSelectorService(
                    $sm->get('doctrine.entitymanager.orm_default')
                );
            },
        )
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);