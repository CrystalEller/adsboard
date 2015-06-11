<?php

return array(

    'router' => array(
        'routes' => array(
            'login' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/login',
                    'defaults' => array(
                        'controller' => 'User\Controller\User',
                        'action' => 'login',
                    ),
                ),
            ),
            'logout' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/logout',
                    'defaults' => array(
                        'controller' => 'User\Controller\User',
                        'action' => 'logout',
                    ),
                ),
            ),
            'registration' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/registration',
                    'defaults' => array(
                        'controller' => 'User\Controller\User',
                        'action' => 'registration',
                    ),
                ),
            ),
            'captchaImage' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/images/captcha/:image',
                    'constraints' => array(
                        'image' => '[a-z0-9]+\.png$'
                    ),
                    'defaults' => array(
                        'controller' => 'User\Controller\Captcha',
                        'action' => 'image',
                    )
                )
            ),
            'confirmRegistration' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/registration/confirmed/:hash',
                    'defaults' => array(
                        'controller' => 'User\Controller\User',
                        'action' => 'confirmRegistration',
                    ),
                ),
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'User\Controller\User' => 'User\Controller\UserController',
            'User\Controller\Captcha' => 'User\Controller\CaptchaController',
        )
    ),

    'controller_plugins' => array(
        'factories' => array(
            'acl' => 'User\Controller\Plugin\Factory\AclControllerPluginFactory',
        )
    ),

    'service_manager' => array(
        'factories' => array(
            'Zend\Session\SessionManager' => 'User\Factory\SessionManagerFactory',
            'User\Form\RegistrationForm' => 'User\Factory\RegistrationFormFactory',
            'Zend\Authentication\AuthenticationService' => function ($serviceManager) {
                return $serviceManager->get('doctrine.authenticationservice.orm_default');
            },
            'User\Service\Search' => 'User\Service\Factory\ElasticSearchServiceFactory'
        )
    ),

    'session' => array(
        'config' => array(
            'class' => 'Zend\Session\Config\SessionConfig',
            'options' => array(
                'name' => 'adsboard',
                'use_cookies' => true,
                'use_only_cookies' => true,
                'cookie_httponly' => true,
            ),
        ),
        'storage' => 'Zend\Session\Storage\SessionArrayStorage',
        'validators' => array(
            'Zend\Session\Validator\RemoteAddr',
            'Zend\Session\Validator\HttpUserAgent',
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);