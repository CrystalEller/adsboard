<?php

namespace User\Service\Factory;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ElasticSearchServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sl)
    {
        return new \User\Service\ElasticSearchService(
            $sl->get('elastica-client'),
            $sl->get('doctrine.entitymanager.orm_default')
        );
    }
}