<?php

namespace Ads\Controller;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class AdsFormController extends AbstractActionController
{
    protected $em;
    protected $nsc;

    public function showFormAdsAction()
    {
        $em = $this->getEntityManager();

        $cats = $em->getRepository('Application\Entity\Categories')
            ->getRootCategories()
            ->getResult(Query::HYDRATE_ARRAY);

        $regions = $em->getRepository('Application\Entity\Region')
            ->getAll()
            ->getResult(Query::HYDRATE_ARRAY);

        $currencies = $em->getRepository('Application\Entity\Currency')
            ->getAll()
            ->getResult(Query::HYDRATE_ARRAY);

        return new ViewModel(array(
            'cats' => $cats,
            'regions' => $regions,
            'currencies' => $currencies
        ));

    }

    public function showUpdateFormAdsAction()
    {
        $adsId = $this->params('adsId', 0);

        if ($this->acl()->belongsToUser('ads', $adsId)) {
            $em = $this->getEntityManager();
            $adsId = $this->params('adsId', 0);

            $ads = $em->createQueryBuilder()
                ->select('a')
                ->from('Application\Entity\Ads', 'a')
                ->where('a.id = ?1')
                ->setParameter(1, $adsId)
                ->getQuery()
                ->getSingleResult(Query::HYDRATE_ARRAY);

            $currencies = $em->getRepository('Application\Entity\Currency')
                ->getAll()
                ->getResult(Query::HYDRATE_ARRAY);

            return new ViewModel(array(
                'ads' => $ads,
                'currencies' => $currencies
            ));
        }
    }

    public function getCitiesAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $cities = $this->getEntityManager()
                ->getRepository('Application\Entity\City')
                ->getCityByRegionId($request->getPost('regionid'))
                ->getResult(Query::HYDRATE_ARRAY);

            return new JsonModel(array(
                'cities' => $cities
            ));
        }
    }

    public function getCategoriesAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $pid = $request->getPost('pid');

            $category = $this->getEntityManager()
                ->find('Application\Entity\Categories', $pid);

            $cats = $this->getNestedSetCategories()
                ->wrapNode($category)->getChildren();

            $data = array();
            $size = sizeof($cats);

            for ($i = 0; $i < $size; $i++) {
                $node = $cats[$i]->getNode();
                $data[$i]['id'] = $node->getId();
                $data[$i]['name'] = $node->getName();
                $data[$i]['level'] = $node->getLevel();
            }

            return new JsonModel(array(
                'cats' => $data
            ));
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
}