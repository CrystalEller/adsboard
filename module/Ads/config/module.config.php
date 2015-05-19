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
            'showAds' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/ads/:adsId',
                    'constraints' => array(
                        'adsId' => '\d+'
                    ),
                    'defaults' => array(
                        'controller' => 'Ads\Controller\Ads',
                        'action' => 'showAds',
                    )
                ),
            ),
            'search' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/search',
                    'defaults' => array(
                        'controller' => 'Ads\Controller\Ads',
                        'action' => 'search',
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
            'Ads\Filter\AdsFilter' => 'Ads\Filter\Factory\AdsFilterFactory',
            'Ads\Filter\PriceFilter' => 'Ads\Filter\Factory\PriceFilterFactory',
            'Ads\Service\Search' => 'Ads\Service\Factory\SearchServiceFactory'
        )
    ),

    'view_helpers' => array(
        'invokables' => array(
            'thumbHelper' => 'Ads\View\Helper\ThumbHelper'
        )
    ),

    'img_thumb' => array(
        // relative to root dir
        'img_root' => './public/img/ads_imgs',
        // relative to root dir
        'thumbs' => './public/img/ads_imgs/thumbs',
        'clear_from_path' => './public/',
        'width' => 170,
        'height' => 170
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