<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'Ads\Controller\Ads',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),

    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'elastica-client' => 'Application\Service\ElasticaClientFactory',
            'Application\Hydrator\Doctrine' => 'Application\Hydrator\Doctrine',
            'Application\Service\NestedSetCategories' => 'Application\Service\NestedSetCategoriesFactory'
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Console\ElasticSearch' => 'Ads\Controller\Console\ElasticSearchController',
        ),
    ),

    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),

    'doctrine' => array(
        'driver' => array(
            'property_entities' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array('./module/Application/src/Application/Entity')
            ),

            'orm_default' => array(
                'drivers' => array(
                    'Application\Entity' => 'property_entities'
                ),
            )
        ),
        'authentication' => array(
            'orm_default' => array(
                'object_manager' => 'Doctrine\ORM\EntityManager',
                'identity_class' => 'Application\Entity\Users',
                'identity_property' => 'email',
                'credential_property' => 'password',
                'credential_callable' => function (\Application\Entity\Users $user, $passwordGiven) {
                    $crypt = new  \Zend\Crypt\Password\Bcrypt();

                    if ($crypt->verify($passwordGiven, $user->getPassword()) && $user->getRole() !== null) {
                        return true;
                    }

                    return false;
                },
            ),
        ),
    ),

    'console' => array(
        'router' => array(
            'routes' => array(
                'elastic-import-ads' => array(
                    'options' => array(
                        'route'    => 'elastic import ads',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Console\ElasticSearch',
                            'action'     => 'importAds'
                        )
                    )
                ),
                'elastic-import-users' => array(
                    'options' => array(
                        'route'    => 'elastic import users',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Console\ElasticSearch',
                            'action'     => 'importUsers'
                        )
                    )
                )
            )
        )
    ),
);
