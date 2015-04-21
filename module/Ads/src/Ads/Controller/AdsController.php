<?php

namespace Ads\Controller;

use Application\Entity\AdsValues;
use Zend\Filter\File\RenameUpload;
use Zend\InputFilter\FileInput;
use Zend\Mvc\Controller\AbstractActionController;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
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
            $files = $request->getFiles();
            $props = $request->getPost('prop');

            $data = array_merge(
                $request->getPost()->toArray(),
                $files->toArray()
            );

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
                $hydrator = new DoctrineHydrator($this->em());
                $ads = new Ads();

                $data = array(
                    'title' => $request->getPost('title'),
                    'description' => $request->getPost('description'),
                    'userid' => $this->identity()->getId(),
                    'categoryid' => end(array_values($request->getPost('category'))),
                    'cityid' => $request->getPost('city'),
                    'regionid' => $request->getPost('region')
                );

                if ($request->getPost('no-price') !== 'no-price') {
                    $data['price'] = $request->getPost('price');
                    $data['currencyid'] = $request->getPost('currency');
                }

                $ads = $hydrator->hydrate($data, $ads);

                $this->em()->persist($ads);
                $this->em()->flush();

                if (!empty($props)) {
                    foreach ($props as $id => $values) {
                        if (!is_array($values)) {
                            $adsValue = new AdsValues();
                            $hydrator->hydrate(array(
                                'attrid' => $id,
                                'adsid' => $ads->getId(),
                                'value' => $values
                            ), $adsValue);
                            $this->em()->persist($adsValue);
                        } else {
                            foreach ($values as $value) {
                                $adsValue = new AdsValues();
                                $hydrator->hydrate(array(
                                    'attrid' => $id,
                                    'adsid' => $ads->getId(),
                                    'value' => $value
                                ), $adsValue);
                                $this->em()->persist($adsValue);
                            }
                        }
                    }
                }

                $this->em()->flush();

                $this->move_uploaded_imgs($files, $ads->getId());

                return new JsonModel(array(
                    'success' => true,
                    'redirect' => $this->url()->fromRoute('home')
                ));
            }

        }
    }

    private function move_uploaded_imgs($files, $adsId)
    {
        if (!empty($files['files'])) {
            $uploadDir = './public/img/ads/';
            foreach ($files['files'] as $file) {
                $newFileName = $uploadDir . $adsId . '_' . $file['name'];
                move_uploaded_file($file['tmp_name'], $newFileName);
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
        $number = $selector->countAdsByCategories();
        $categories = $this->em()
            ->createQuery('Select partial c.{name, id} from Application\Entity\Categories c WHERE c.id=c.root')
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return new ViewModel(array(
            'ads' => $ads,
            'page' => $page,
            'numberAds' => $number,
            'adsMenu' => array(
                'type' => 'category',
                'data' => $categories
            )
        ));
    }

    public function adsByCategoryAction()
    {
        $request = $this->getRequest();
        $page = $request->getQuery('page', 0);
        $catId = $this->params('catId', 0);
        $selector = $this->getServiceLocator()->get('Ads\Service\AdsSelector');

        $category = $this->em()
            ->find('Application\Entity\Categories', $catId);

        if (!empty($category)) {
            $catsIds = array();
            $cats = $this->nsm('Application\Entity\Categories')
                ->wrapNode($category)
                ->getDescendants();

            if (!empty($cats)) {
                $catsData = array();

                foreach ($cats as $cat) {
                    $catsData[]['id'] = $catsIds[] = $cat->getId();
                    $catsData[]['name'] = $cat->__toString();
                }
            } else {
                $catsData[]['id'] = $catsIds[] = $category->getId();
                $catsData[]['name'] = $category->getName();

                $attributes = $this->em()
                    ->createQuery("Select a.name, c.value from Application\Entity\AdsValues c
                                      JOIN c.attrid a
                                      WHERE a.catid=:category")
                    ->setParameters(array(
                        'category' => $category->getId(),
                    ))
                    ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            }

            $ads = $selector->getAdsByCategories($page, $catsIds);
            $number = $selector->countAdsByCategories($catsIds);

            return new ViewModel(array(
                'ads' => $ads,
                'numberAds' => $number,
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

    public function searchByFixedParamsAction()
    {
        $request = $this->getRequest();
        $page = $request->getQuery('page', 0);
        $data = $request->getPost();
        $search = $this->getServiceLocator()->get('Ads\Service\Search');
        $selector = $this->getServiceLocator()->get('Ads\Service\AdsSelector');

        $ads = $search->setData($data['search'])->search($page);
        $attrsVals = $selector->getAttrValuesByCategory($data['categoryId']);
        $number = $search->countAds();

        return new ViewModel(array(
            'ads' => $ads,
            'numberAds' => $number,
            'page' => $page,
            'adsMenu' => array(
                'type' => 'attribute',
                'data' => $attrsVals
            ),
            'search' => $data['search']
        ));

    }
}

