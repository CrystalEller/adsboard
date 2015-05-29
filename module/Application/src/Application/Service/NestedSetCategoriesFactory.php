<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 18.05.15
 * Time: 22:07
 */

namespace Application\Service;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineExtensions\NestedSet\Config;
use DoctrineExtensions\NestedSet\Manager;

class NestedSetCategoriesFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sl)
    {
        $entityManager = $sl->get('doctrine.entitymanager.orm_default');
        $config = new Config($entityManager, 'Application\Entity\Categories');
        $nsm = new Manager($config);

        return $nsm;
    }
}