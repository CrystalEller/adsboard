<?php

namespace Ads\Service\Factory;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SearchServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sl)
    {
        return new \Ads\Service\SearchService(
            $sl->get('doctrine.entitymanager.orm_default')
        );
    }
}