<?php

namespace User\Controller\Plugin\Factory;


use User\Controller\Plugin\AclControllerPlugin;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AclControllerPluginFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm)
    {
        $sl = $sm->getServiceLocator();

        return new AclControllerPlugin(
            $sl->get('doctrine.authenticationservice.orm_default')
        );
    }
}