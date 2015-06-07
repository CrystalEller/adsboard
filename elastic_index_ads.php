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
$nsc = $serviceManager->get('Application\Service\NestedSetCategories');

$documents = array();
$ads = $em->getRepository('Application\Entity\Ads')->findAll();
$qb = $em->createQueryBuilder();

if (!empty($ads)) {
    foreach ($ads as $key => $val) {

        $values = $qb
            ->resetDQLParts()
            ->select('av, val, attr')
            ->from('Application\Entity\AdsValues', 'av')
            ->join('av.valueid', 'val')
            ->join('val.attrid', 'attr')
            ->where('av.adsid=?1')
            ->setParameter(1, $val->getId())
            ->distinct()
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $valuesIds = array_column($values, 'valueid');
        $value = array_column($valuesIds, 'value');

        $attrsIds = array_column($valuesIds, 'attrid');
        $attrsValues = array_unique(array_column($attrsIds, 'name'));

        $catsIds = array();
        $cats = $nsc->wrapNode($val->getCategoryid())->getAncestors();

        foreach ($cats as $cat) {
            $cat = $cat->getNode();
            $catsIds[] = $cat->getId();
        }

        $doc = array(
            'id' => $val->getId(),
            'title' => $val->getTitle(),
            'description' => $val->getDescription(),
            'category' => $catsIds,
            'region' => $val->getRegionid()->getId(),
            'city' => $val->getCityid()->getId(),
            'props' => array(
                'attr' => $attrsValues,
                'values' => $value
            )
        );

        $documents[] = new \Elastica\Document($val->getId(), $doc, 'ads', 'adsboard');
    }

    $elasticClient->addDocuments($documents);

    $elasticClient->getIndex('adsboard')->refresh();
}
