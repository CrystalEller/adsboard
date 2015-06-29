<?php
return array(

    'router' => array(
        'routes' => array(
            'showFormAds' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/showFormAds',
                    'defaults' => array(
                        'controller' => 'Ads\Controller\AdsForm',
                        'action' => 'showFormAds',
                    ),
                ),
            ),
            'getCities' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/getCities',
                    'defaults' => array(
                        'controller' => 'Ads\Controller\AdsForm',
                        'action' => 'getCities',
                    ),
                ),
            ),
            'getRegions' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/getRegions',
                    'defaults' => array(
                        'controller' => 'Ads\Controller\AdsForm',
                        'action' => 'getRegions',
                    ),
                ),
            ),
            'getRootCategories' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/getRootCategories',
                    'defaults' => array(
                        'controller' => 'Ads\Controller\AdsForm',
                        'action' => 'getRootCategories',
                    ),
                ),
            ),
            'getCategories' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/getCategories',
                    'defaults' => array(
                        'controller' => 'Ads\Controller\AdsForm',
                        'action' => 'getCategories',
                    ),
                ),
            ),
            'createAds' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/createAds',
                    'defaults' => array(
                        'controller' => 'Ads\Controller\UserAds',
                        'action' => 'createAds',
                    ),
                ),
            ),
            'getUserAds' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/user/showAds',
                    'defaults' => array(
                        'controller' => 'Ads\Controller\UserAds',
                        'action' => 'getAds',
                    ),
                ),
            ),
            'updateUserAds' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/user/updateAds/:adsId',
                    'constraints' => array(
                        'adsId' => '\d+'
                    )
                ),
                'may_terminate' => false,
                'child_routes' => array(
                    'getUpdateAdsForm' => array(
                        'type' => 'Zend\Mvc\Router\Http\Method',
                        'options' => array(
                            'verb' => 'get',
                            'defaults' => array(
                                'controller' => 'Ads\Controller\AdsForm',
                                'action' => 'showUpdateFormAds',
                            ),
                        )
                    ),
                    'postUpdateAdsForm' => array(
                        'type' => 'Zend\Mvc\Router\Http\Method',
                        'options' => array(
                            'verb' => 'post',
                            'defaults' => array(
                                'controller' => 'Ads\Controller\UserAds',
                                'action' => 'updateAds',
                            ),
                        )
                    )
                )
            ),
            'deleteUserAds' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/user/deleteAds/:adsId',
                    'constraints' => array(
                        'adsId' => '\d+'
                    ),
                    'defaults' => array(
                        'controller' => 'Ads\Controller\UserAds',
                        'action' => 'deleteAds',
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
            'adsByAttributes' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/category/:catId/attributes',
                    'constraints' => array(
                        'catId' => '\d+'
                    ),
                    'defaults' => array(
                        'controller' => 'Ads\Controller\Ads',
                        'action' => 'adsByAttributes',
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
            'sendComplainMessage' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/ads/:adsId/message/create',
                    'constraints' => array(
                        'adsId' => '\d+'
                    ),
                    'defaults' => array(
                        'controller' => 'Ads\Controller\Message',
                        'action' => 'create',
                    ),
                ),
            ),
            'deleteComplainMessage' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/ads/message/delete/:msgId',
                    'constraints' => array(
                        'msgId' => '\d+'
                    ),
                    'defaults' => array(
                        'controller' => 'Ads\Controller\Message',
                        'action' => 'delete',
                    ),
                ),
            ),
            'getComplainMessages' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/ads/getMessages',
                    'defaults' => array(
                        'controller' => 'Ads\Controller\Message',
                        'action' => 'getMessages',
                    ),
                ),
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'Ads\Controller\Ads' => 'Ads\Controller\AdsController',
            'Ads\Controller\UserAds' => 'Ads\Controller\UserAdsController',
            'Ads\Controller\AdsForm' => 'Ads\Controller\AdsFormController',
            'Ads\Controller\Message' => 'Ads\Controller\MessageController',
        ),
    ),

    'service_manager' => array(
        'factories' => array(
            'Ads\Filter\AdsFilter' => 'Ads\Filter\Factory\AdsFilterFactory',
            'Ads\Filter\PriceFilter' => 'Ads\Filter\Factory\PriceFilterFactory',
            'Ads\Service\Search' => 'Ads\Service\Factory\SearchServiceFactory',
            'elastic-ads' => 'Ads\Service\Factory\ElasticSearchServiceFactory'
        ),
        'invokables' => array(
            'Ads\Filter\AdsUpdateFilter' => 'Ads\Filter\AdsUpdateFilter',
            'Ads\Filter\Message' => 'Ads\Filter\MessageFilter',
        ),
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