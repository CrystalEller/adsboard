<?php

namespace Ads\Filter\Factory;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AdsFilterFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sl)
    {
        $entityManager = $sl->get('doctrine.entitymanager.orm_default');
        $config = new \DoctrineExtensions\NestedSet\Config($entityManager, 'Application\Entity\Categories');
        $nsm = new \DoctrineExtensions\NestedSet\Manager($config);

        return new \Ads\Filter\AdsFilter($entityManager, $nsm);
    }
}