<?php

namespace Application\Service;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ElasticaClientFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sl)
    {
        $config = $sl->get('Config');
        $clientOptions = isset($config['elastica']) ? $config['elastica'] : array();
        $client = new \Elastica\Client($clientOptions);

        return $client;
    }
}