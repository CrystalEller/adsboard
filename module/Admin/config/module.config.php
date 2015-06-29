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
                        'controller' => 'Admin\Controller\Users',
                        'action' => 'users',
                    ),
                ),
            ),
            'getUsers' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin/getUsers',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Users',
                        'action' => 'getUsers',
                    ),
                ),
            ),
            'updateUsers' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin/user/update',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Users',
                        'action' => 'update',
                    ),
                ),
            ),
            'getUserAdsPreview' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/user/:userId/showAdsPreview',
                    'constraints' => array(
                        'userId' => '\d+'
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Ads',
                        'action' => 'getAdsPreview',
                    ),
                ),
            ),
            'adminDeleteAds' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/user/ads/delete/:adsId',
                    'constraints' => array(
                        'adsId' => '\d+'
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Ads',
                        'action' => 'deleteAds',
                    ),
                ),
            ),
            'adminGetUserAds' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/user/ads/:adsId',
                    'constraints' => array(
                        'adsId' => '\d+'
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Ads',
                        'action' => 'getAds',
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
            'adminMessages' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin/messages',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Message',
                        'action' => 'messages',
                    )
                )
            ),
            'getAdminMessages' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin/getMessages',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Message',
                        'action' => 'get',
                    )
                )
            ),
            'deleteAdminMessage' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/message/delete/:msgId',
                    'constraints' => array(
                        'action' => '\d+'
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Message',
                        'action' => 'delete',
                    )
                )
            ),
            'sendAdminMessage' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin/message/answer',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Message',
                        'action' => 'answer',
                    )
                )
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'Admin\Controller\Admin' => 'Admin\Controller\AdminController',
            'Admin\Controller\Category' => 'Admin\Controller\CategoryController',
            'Admin\Controller\Users' => 'Admin\Controller\UsersController',
            'Admin\Controller\Ads' => 'Admin\Controller\AdsController',
            'Admin\Controller\Message' => 'Admin\Controller\MessageController'
        ),
    ),

    'service_manager' => array(
        'factories' => array(
            'Admin\Form\CategoryFilter' => function ($sm) {
                return new \Admin\Form\CategoryFilter(
                    $sm->get('doctrine.entitymanager.orm_default')
                );
            },
        ),
        'invokables' => array(
            'Admin\Filter\EmailMessage' => 'Admin\Filter\EmailMessageFilter'
        ),
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
