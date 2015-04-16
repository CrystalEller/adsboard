<?php

namespace Application\ControllerPlugin;


use DoctrineExtensions\NestedSet\Config;
use DoctrineExtensions\NestedSet\Manager;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class NestedSetPlugin extends AbstractPlugin
{
    public function __invoke($entityFullName)
    {
        $entityManager = $this->getController()->em();
        $config = new Config($entityManager, $entityFullName);
        $nsm = new Manager($config);

        return $nsm;
    }
}