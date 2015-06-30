<?php

namespace Application\Controller;


use Doctrine\ORM\EntityManager;
use Elastica\Document;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Request as ConsoleRequest;

class ElasticSearchController extends AbstractActionController
{
    protected $em;
    protected $nsc;
    protected $elasticClient;

    public function importAdsAction()
    {
        $request = $this->getRequest();

        if (!$request instanceof ConsoleRequest){
            throw new \RuntimeException('You can only use this action from a console!');
        }

        $elasticClient = $this->getElasticaClient();
        $em = $this->getEntityManager();
        $nsc = $this->getNestedSetCategories();

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
                    'price' => $val->getPrice() ?: null,
                    'props' => array(
                        'attr' => $attrsValues,
                        'values' => $value
                    )
                );

                $documents[] = new Document($val->getId(), $doc, 'ads', 'adsboard');
            }

            $elasticClient->addDocuments($documents);

            $elasticClient->getIndex('adsboard')->refresh();

            return "Done! All records are successfully imported to Elastic Search Engine";
        }

    }

    public function importUsersAction()
    {
        $request = $this->getRequest();

        if (!$request instanceof ConsoleRequest){
            throw new \RuntimeException('You can only use this action from a console!');
        }

        $elasticClient = $this->getElasticaClient();
        $em = $this->getEntityManager();

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

                $documents[] = new Document($val->getId(), $doc, 'users', 'adsboard');
            }

            $elasticClient->addDocuments($documents);

            $elasticClient->getIndex('adsboard')->refresh();

            return "Done! All records are successfully imported to Elastic Search Engine";
        }
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;

        return $this;
    }

    public function getEntityManager()
    {
        if (!$this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

    public function getNestedSetCategories()
    {
        if (!$this->nsc) {
            $this->nsc = $this->getServiceLocator()->get('Application\Service\NestedSetCategories');
        }
        return $this->nsc;
    }

    public function getElasticaClient()
    {
        if (!$this->elasticClient) {
            $this->elasticClient = $this->getServiceLocator()->get('elastica-client');
        }
        return $this->elasticClient;
    }
}