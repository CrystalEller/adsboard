<?php

namespace Ads\Controller;

use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\Query;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Application\Entity\Ads;


class AdsController extends AbstractActionController
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

    public function createAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $messages = array();
            $filter = $this->getServiceLocator()->get('Ads\Filter\AdsFilter');
            $filterPrice = $this->getServiceLocator()->get('Ads\Filter\PriceFilter');
            $validator = $this->getServiceLocator()
                ->get('ValidatorManager')
                ->get('FormBuilder');
            $props = $request->getPost('prop');
            $values = $request->getPost()->toArray();
            $files = $request->getFiles()->toArray();


            if (empty($files['files'][0])) {
                unset($values['files']);
                $files = array();
            }

            $data = array_merge($values, $files);

            $filter->setData($data);
            $filterPrice->setData($data);

            if ($request->getPost('no-price') !== 'no-price') {
                $filterPrice->setValidationGroup(array('price', 'currency'));
            } else {
                $filterPrice->setValidationGroup('no-price');
            }

            if (!$filter->isValid() |
                !$validator->isValid($props) |
                !$filterPrice->isValid()
            ) {
                $messages = array_merge(
                    $filter->getMessages(),
                    $validator->getMessages(),
                    $filterPrice->getMessages()
                );
            }

            if (sizeof($messages) > 0) {
                return new JsonModel(array(
                    'success' => false,
                    'formErrors' => $messages
                ));
            } else {
                $em = $this->getEntityManager();
                $hydrator = $this->getServiceLocator()->get('Application\Hydrator\Doctrine');
                $ads = new Ads();

                $data = array(
                    'title' => $request->getPost('title'),
                    'description' => $request->getPost('description'),
                    'userid' => $this->identity()->getId(),
                    'categoryid' => end(array_values($request->getPost('category'))),
                    'cityid' => $request->getPost('city'),
                    'regionid' => $request->getPost('region'),
                    'userName' => $request->getPost('userName'),
                    'telephone' => $request->getPost('telephone')
                );

                if ($request->getPost('no-price') !== 'no-price') {
                    $data['price'] = $request->getPost('price');
                    $data['currencyid'] = $request->getPost('currency');
                }

                $ads = $hydrator->hydrate($data, $ads);

                $em->persist($ads);
                $em->flush();

                $em->getRepository('Application\Entity\AdsValues')
                    ->saveValues($props, $ads);

                $this->move_uploaded_imgs($files, $ads->getId());

                return new JsonModel(array(
                    'success' => true,
                    'redirect' => $this->url()->fromRoute('home')
                ));
            }

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

    public function getCitiesAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $cities = $this->em()
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

            $category = $this->em()
                ->find('Application\Entity\Categories', $pid);

            $cats = $this->nsm('Application\Entity\Categories')
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
            'adsMenu' => array(
                'type' => 'category',
                'data' => $categories
            )
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
                $catsData[] = array(
                    'id' => $category->getId(),
                    'name' => $category->getName()
                );

                $attributes = $em->getRepository('Application\Entity\CategoryAttributesValues')
                    ->getValuesByCategoryId($category->getId(), 'user')
                    ->getResult(Query::HYDRATE_ARRAY);
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
                'adsMenu' => array(
                    'type' => !empty($cats) ? 'category' : 'attribute',
                    'data' => !empty($cats) ? $catsData : $attributes
                )
            ));
        } else {
            $viewModel = new ViewModel();
            $viewModel->setTemplate('error/404');
            return $viewModel;
        }
    }

    public function searchAction()
    {
        $limit = 10;
        $request = $this->getRequest();
        $page = $request->getQuery('page', 0);
        $data = $request->getQuery()->toArray();
        $em = $this->getEntityManager();
        $search = $this->getServiceLocator()->get('Ads\Service\Search');
        $view = new ViewModel();

        $ads = $search->setData($data)->search($page, $limit);
        $number = $search->countAds();

        $attrsVals = $em->getRepository('Application\Entity\CategoryAttributesValues')
            ->getValuesByCategoryId($data['categoryid'], 'user')
            ->getResult(Query::HYDRATE_ARRAY);

        $view->setTemplate('ads/ads/ads-by-category')
            ->setVariables(array(
                'ads' => $ads,
                'numberAds' => $number,
                'page' => $page,
                'limit' => $limit,
                'search' => $data,
                'adsMenu' => array(
                    'type' => 'attribute',
                    'data' => $attrsVals
                ),
            ));

        return $view;
    }


    private function move_uploaded_imgs($files, $adsId)
    {
        if (!empty($files['files'])) {
            $uploadDir = './public/img/ads_imgs/';
            foreach ($files['files'] as $file) {
                $newFileName = $uploadDir . $adsId . '_' . $file['name'];
                move_uploaded_file($file['tmp_name'], $newFileName);
            }
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

