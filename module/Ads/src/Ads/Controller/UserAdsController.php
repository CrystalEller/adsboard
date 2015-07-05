<?php

namespace Ads\Controller;


use Application\Entity\Ads;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class UserAdsController extends AbstractActionController
{
    protected $em;
    protected $elasticSearch;

    public function createAdsAction()
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
                $elasticService = $this->getElasticSearchService();
                $hydrator = $this->getServiceLocator()->get('Application\Hydrator\Doctrine');
                $ads = new Ads();

                $data = array(
                    'title' => $request->getPost('title'),
                    'description' => $request->getPost('description'),
                    'userid' => $this->identity()->getId(),
                    'categoryid' => end(array_values($request->getPost('category'))),
                    'cityid' => $request->getPost('city')?:null,
                    'regionid' => $request->getPost('region')?:null,
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


                $elasticService->saveAds(
                    $ads,
                    array_values($request->getPost('category')),
                    $props
                );

                $this->update_uploaded_imgs($files, $ads->getId());

                return new JsonModel(array(
                    'success' => true,
                    'redirect' => $this->url()->fromRoute('home')
                ));
            }
        }
    }

    public function deleteAdsAction()
    {
        $adsId = $this->params()->fromRoute('adsId', 0);

        if ($this->acl()->belongsToUser('ads', $adsId)) {
            $em = $this->getEntityManager();
            $ads = $em->find('Application\Entity\Ads', $adsId);
            $elasticService = $this->getElasticSearchService();

            $values = $em->createQueryBuilder()
                ->select('av, v')
                ->from('Application\Entity\AdsValues', 'av')
                ->join('av.valueid', 'v')
                ->where('av.adsid = ?1 AND v.owner != ?2')
                ->setParameter(1, 32)
                ->setParameter(2, 'admin')
                ->getQuery()
                ->getResult();

            array_walk($values, function ($item) use ($em) {
                $em->remove($item);
            });

            $em->remove($ads);
            $em->flush();

            $elasticService->deleteAds(intval($adsId));

            return new JsonModel(array(1));
        } else {
            return new JsonModel(array(0));
        }
    }

    public function getAdsAction()
    {
        $view = new ViewModel();
        $em = $this->getEntityManager();
        $userId = $this->identity()->getId();
        $page = $this->params()->fromQuery('page', 0);
        $limit = 10;

        $ads = $em->getRepository('Application\Entity\Ads')
            ->getAdsByUserId($userId, $page)
            ->getResult(Query::HYDRATE_ARRAY);

        $countAds = $em->getRepository('Application\Entity\Ads')
            ->countAdsByUserId($userId)
            ->getSingleScalarResult();

        $view->setVariables(array(
            'ads' => $ads,
            'page' => $page,
            'limit' => $limit,
            'numberAds' => $countAds
        ));

        return $view;
    }

    public function updateAdsAction()
    {
        $adsId = $this->params('adsId', 0);
        $request = $this->getRequest();
        $em = $this->getEntityManager();

        if ($this->acl()->belongsToUser('ads', $adsId)) {
            $messages = array();
            $adsFilter = $this->getServiceLocator()->get('Ads\Filter\AdsUpdateFilter');
            $priceFilter = $this->getServiceLocator()->get('Ads\Filter\PriceFilter');

            $values = $request->getPost()->toArray();
            $files = $request->getFiles()->toArray();
            $deleteFiles = $request->getPost('deleteImgs');

            if (empty($files['files'][0])) {
                unset($values['files']);
                $files = array();
            }

            $data = array_merge($values, $files);

            $adsFilter->setData($data);
            $priceFilter->setData($data);

            if ($request->getPost('no-price') !== 'no-price') {
                $priceFilter->setValidationGroup(array('price', 'currency'));
            } else {
                $priceFilter->setValidationGroup('no-price');
            }

            if (!$adsFilter->isValid() |
                !$priceFilter->isValid()
            ) {
                $messages = array_merge(
                    $adsFilter->getMessages(),
                    $priceFilter->getMessages()
                );
            }

            if (sizeof($messages) > 0) {
                return new JsonModel(array(
                    'success' => false,
                    'formErrors' => $messages
                ));
            } else {
                $hydrator = $this->getServiceLocator()->get('Application\Hydrator\Doctrine');
                $elasticService = $this->getElasticSearchService();

                $ads = new Ads();

                $data = array(
                    'id' => $adsId,
                    'title' => $request->getPost('title'),
                    'description' => $request->getPost('description'),
                    'userName' => $request->getPost('userName'),
                    'telephone' => $request->getPost('telephone')
                );

                if ($request->getPost('no-price') !== 'no-price') {
                    $data['price'] = $request->getPost('price');
                    $data['currencyid'] = $request->getPost('currency');
                }

                $ads = $hydrator->hydrate($data, $ads);

                $ads = $em->merge($ads);
                $em->flush();

                $elasticService->updateAds($ads);

                $this->update_uploaded_imgs($files, $adsId, $deleteFiles);

                return new JsonModel(array(
                    'success' => true,
                    'redirect' => $this->url()->fromRoute('showAds', array('adsId' => $adsId))
                ));
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

    public function getElasticSearchService()
    {
        if (!$this->elasticSearch) {
            $this->elasticSearch = $this->getServiceLocator()->get('elastic-ads');
        }
        return $this->elasticSearch;
    }

    private function update_uploaded_imgs($uploadFiles, $adsId, $deleteImgs = null)
    {
        $uploadDir = './public/img/ads_imgs/';
        $thumbsDir = './public/img/ads_imgs/thumbs/';

        if (!empty($deleteImgs)) {
            $deleteImgs = json_decode($deleteImgs);

            foreach ($deleteImgs as $deleteImg) {
                $thumbs = glob($thumbsDir . $adsId . "_*");
                if (!empty($thumbs)) {
                    $partImg = end(preg_split("#_#", basename($deleteImg)));

                    foreach ($thumbs as $thumb) {
                        $partThumb = end(preg_split("#_#", basename($thumb)));
                        if (strcasecmp($partImg, $partThumb) == 0 &&
                            file_exists($thumb)
                        ) {
                            unlink($thumb);
                        }
                    }
                }
                if (file_exists($uploadDir . $deleteImg)) {
                    unlink($uploadDir . $deleteImg);
                }
            }
        }

        if (!empty($uploadFiles['files'])) {
            foreach ($uploadFiles['files'] as $file) {
                $newFileName = $uploadDir . $adsId . '_' . $file['name'];
                move_uploaded_file($file['tmp_name'], $newFileName);
            }
        }
    }
}