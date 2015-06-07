<?php

namespace Ads\Service\Factory;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ElasticSearchServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sl)
    {
        return new \Ads\Service\ElasticSearchService(
            $sl->get('elastica-client'),
            $sl->get('doctrine.entitymanager.orm_default')
        );
    }
}