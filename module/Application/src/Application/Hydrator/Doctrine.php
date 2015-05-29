<?php

namespace Application\Hydrator;


use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Doctrine implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sl)
    {
        $entityManager = $sl->get('doctrine.entitymanager.orm_default');

        return new DoctrineHydrator($entityManager);
    }
}