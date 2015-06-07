<?php

namespace Ads\Controller;

use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\Query;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Application\Entity\Ads;


class AdsController extends AbstractActionController
{
    protected $em;
    protected $nsc;

    public function onDispatch(MvcEvent $e)
    {
        $this->initLayout();
        return parent::onDispatch($e);
    }

    public function initLayout()
    {
        $em = $this->getEntityManager();
        $request = $this->getRequest();
        $data = $request->getQuery()->toArray();

        if (!empty($data['regionId'])) {
            $this->layout()->setVariable(
                'region',
                $em->find('Application\Entity\Region', $data['regionId'])
            );
        } elseif (!empty($data['cityId'])) {
            $this->layout()->setVariable(
                'city',
                $em->find('Application\Entity\City', $data['cityId'])
            );
        }

        if (!empty($data['categoryId'])) {
            $this->layout()->setVariable(
                'category',
                $em->find('Application\Entity\Categories', $data['categoryId'])
            );
        }

        if (!empty($data['query'])) {
            $this->layout()->setVariable(
                'query',
                $data['query']
            );
        }
    }

    public function showAdsAction()
    {
        $adsId = $this->params('adsId', 0);
        $em = $this->getEntityManager();

        $ads = $em->find('Application\Entity\Ads', $adsId);

        $props = $em->getRepository('Application\Entity\AdsValues')
            ->findBy(array('adsid' => $adsId));

        $propsGroups = array();

        foreach ($props as $prop) {
            $attrName = $prop->getValueid()->getAttrid()->getName();
            $propsGroups[$attrName][] = $prop->getValueid()->getValue();
        }

        return new ViewModel(array(
            'ads' => $ads,
            'props' => $propsGroups
        ));
    }

    public function mainCategoriesAction()
    {
        $request = $this->getRequest();
        $page = $request->getQuery('page', 0);
        $em = $this->getEntityManager();
        $limit = 10;

        $ads = $em->getRepository('Application\Entity\Ads')
            ->getAdsAll($page, $limit)
            ->getResult(Query::HYDRATE_ARRAY);

        $number = $em->getRepository('Application\Entity\Ads')
            ->countAdsAll()
            ->getSingleScalarResult();

        $categories = $em->getRepository('Application\Entity\Categories')
            ->getRootCategories()
            ->getResult(Query::HYDRATE_ARRAY);

        return new ViewModel(array(
            'ads' => $ads,
            'page' => $page,
            'numberAds' => $number,
            'limit' => $limit,
            'categories' => $categories
        ));
    }

    public function adsByCategoryAction()
    {
        $request = $this->getRequest();
        $em = $this->getEntityManager();
        $nsc = $this->getNestedSetCategories();
        $page = $request->getQuery('page', 0);
        $catId = $this->params('catId', 0);
        $limit = 10;

        $category = $em->find('Application\Entity\Categories', $catId);

        if (!empty($category)) {
            $catsIds = array();
            $catsIds[] = $category->getId();

            $cats = $nsc->wrapNode($category)->getDescendants();

            if (!empty($cats)) {
                $catsData = array();

                foreach ($cats as $cat) {
                    $cat = $cat->getNode();
                    $catsIds[] = $cat->getId();
                    $catsData[] = array(
                        'id' => $cat->getId(),
                        'name' => $cat->getName()
                    );
                }
            } else {
                return $this->redirect()->toRoute('adsByAttributes', array(
                    'catId' => $catId
                ));
            }

            $ads = $em->getRepository('Application\Entity\Ads')
                ->getAdsByCategories($page, $catsIds, $limit)
                ->getResult(Query::HYDRATE_ARRAY);

            $numberAds = $em->getRepository('Application\Entity\Ads')
                ->countAdsByCategories($catsIds)
                ->getSingleScalarResult();

            return new ViewModel(array(
                'ads' => $ads,
                'numberAds' => $numberAds,
                'limit' => $limit,
                'category' => end($catsIds),
                'page' => $page,
                'categories' => $catsData
            ));
        }
    }

    public function adsByAttributesAction()
    {
        $request = $this->getRequest();
        $em = $this->getEntityManager();
        $page = $request->getQuery('page', 0);
        $catId = $this->params('catId', 0);
        $limit = 10;

        $category = $em->find('Application\Entity\Categories', $catId);

        if (!empty($category)) {

            $attributes = $em->getRepository('Application\Entity\CategoryAttributesValues')
                ->getValuesByCategoryId($category->getId(), 'admin')
                ->getResult(Query::HYDRATE_ARRAY);

            $ads = $em->getRepository('Application\Entity\Ads')
                ->getAdsByCategories($page, $category->getId(), $limit)
                ->getResult(Query::HYDRATE_ARRAY);

            $numberAds = $em->getRepository('Application\Entity\Ads')
                ->countAdsByCategories($category->getId())
                ->getSingleScalarResult();

            return new ViewModel(array(
                'ads' => $ads,
                'numberAds' => $numberAds,
                'limit' => $limit,
                'category' => $category->getId(),
                'page' => $page,
                'attributes' => $attributes
            ));
        }
    }

    public function searchAction()
    {
        $request = $this->getRequest();
        $page = $request->getQuery('page', 0);
        $data = $request->getQuery()->toArray();
        $em = $this->getEntityManager();
        $nsc = $this->getNestedSetCategories();
        $search = $this->getServiceLocator()->get('Ads\Service\Search');
        $viewModel = new ViewModel();
        $limit = 10;

        $ads = $search->setData($data)->search($page, $limit);
        $number = $search->countAds();

        $viewModel->setVariables(array(
            'ads' => $ads,
            'numberAds' => $number,
            'page' => $page,
            'limit' => $limit,
            'search' => $data
        ));

        if (!empty($data['categoryId'])) {
            $category = $em->find('Application\Entity\Categories', $data['categoryId']);
            $cats = $nsc->wrapNode($category)->getChildren();

            if (!empty($cats)) {
                $catsData = array();

                foreach ($cats as $cat) {
                    $cat = $cat->getNode();
                    $catsData[] = array(
                        'id' => $cat->getId(),
                        'name' => $cat->getName()
                    );
                }

                $viewModel->setVariable('categories', $catsData);
            } else {
                $attrsVals = $em->getRepository('Application\Entity\CategoryAttributesValues')
                    ->getValuesByCategoryId($data['categoryId'], 'admin')
                    ->getResult(Query::HYDRATE_ARRAY);

                $viewModel->setVariable('attributes', $attrsVals);
            }
        }

        return $viewModel;
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

