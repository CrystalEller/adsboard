<?php

namespace Ads\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Application\Entity\Ads;


class AdsController extends AbstractActionController
{
    public function showFormAdsAction()
    {
        $cats = $this->em()
            ->createQuery('Select c.id, c.name from Application\Entity\Categories c WHERE c.id=c.root')
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $regions = $this->em()
            ->createQuery('Select r from Application\Entity\Region r')
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $currencies = $this->em()
            ->createQuery('Select c from Application\Entity\Currency c')
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

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

            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $filter->setData($data);
            $filterPrice->setData($data);

            if ($request->getPost('no-price') !== 'no-price') {
                $filterPrice->setValidationGroup(array('price', 'currency'));
            } else {
                $filterPrice->setValidationGroup('no-price');
            }

            if (!$filter->isValid() |
                !$validator->isValid($request->getPost('prop')) |
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
                $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');

                $loggedUser = $authService->getIdentity();
                $category = $this->em()
                    ->getRepository('Application\Entity\Categories')
                    ->find(intval(end(array_values($request->getPost('category')))));
                $city = $this->em()
                    ->getRepository('Application\Entity\City')
                    ->find(intval($request->getPost('city')));
                $currency = $this->em()
                    ->getRepository('Application\Entity\Currency')
                    ->find(intval($request->getPost('currency')));


                $ads = new Ads();

                $ads->setUserid($loggedUser)
                    ->setCategoryid($category)
                    ->setCityid($city)
                    ->setDescription($request->getPost('description'))
                    ->setTitle($request->getPost('title'));

                if ($request->getPost('no-price') !== 'no-price') {
                    $ads->setPrice($request->getPost('price'))
                        ->setCurrencyid($currency);
                } else {
                    $ads->setPrice(0)
                        ->setCurrencyid(null);
                }

                $this->em()->persist($ads);
                $this->em()->flush();

                return new JsonModel(array(
                    'success' => true,
                    'redirect' => $this->url()->fromRoute('home')
                ));
            }

        }
    }

    public function getCitiesAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $cities = $this->em()
                ->createQuery('Select c from Application\Entity\City c WHERE c.regionid=:regionid')
                ->setParameters(array('regionid' => $request->getPost('regionid')))
                ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

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
        $selector = $this->getServiceLocator()->get('Ads\Service\AdsSelector');

        $ads = $selector->getAdsByCategories($page);
        $categories = $this->em()
            ->createQuery('Select c.{name, id} from Application\Entity\Categories c WHERE c.id=c.root')
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return new ViewModel(array(
            'ads' => $ads,
            'adsMenu' => array(
                'type' => 'category',
                'data' => $categories
            )
        ));
    }

    public function adsByCategoryAction()
    {

    }

    public function adsByAttributeAction()
    {
        
    }
}

