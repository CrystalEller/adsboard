<?php

use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;

require 'init_autoloader.php';

$configuration = include 'config/application.config.php';

$serviceManager = new ServiceManager(new ServiceManagerConfig());
$serviceManager->setService('ApplicationConfig', $configuration);

$serviceManager->get('ModuleManager')->loadModules();

$elasticClient = $serviceManager->get('elastica-client');
$em = $serviceManager->get('doctrine.entitymanager.orm_default');

$documents = array();
$users = $em->getRepository('Application\Entity\Users')->findAll();
$qb = $em->createQueryBuilder();

if (!empty($users)) {
    foreach ($users as $key => $val) {

        $doc = array(
            'id' => $val->getId(),
            'email' => $val->getEmail(),
            'role' => $val->getRole(),
            'stat' => $val->getStat()
        );

        $documents[] = new \Elastica\Document($val->getId(), $doc, 'users', 'adsboard');
    }

    $elasticClient->addDocuments($documents);

    $elasticClient->getIndex('adsboard')->refresh();
}
