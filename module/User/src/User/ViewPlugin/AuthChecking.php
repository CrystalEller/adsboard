<?php

namespace User\ViewPlugin;


use Zend\Debug\Debug;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;

class AuthChecking extends AbstractHelper implements ServiceLocatorAwareInterface
{

    private $serviceLocator;

    public function __invoke()
    {
        $authenticationService = $this->getServiceLocator()
            ->getServiceLocator()
            ->get('Zend\Authentication\AuthenticationService');

        $loggedUser = $authenticationService->getIdentity();
        return $loggedUser;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }
}