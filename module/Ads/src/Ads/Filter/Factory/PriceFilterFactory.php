<?php

namespace Ads\Filter\Factory;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PriceFilterFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sl)
    {
        return new \Ads\Filter\PriceFilter(
            $sl->get('doctrine.entitymanager.orm_default')
        );
    }
}