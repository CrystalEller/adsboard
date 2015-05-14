<?php

namespace Admin\Controller;


use Doctrine\ORM\Query;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class AdsController extends AbstractActionController
{
    public function deleteAdsAction()
    {
        $adsId = $this->params('adsId');

        $this->em()->createQueryBuilder()
            ->delete('Application\Entity\Ads', 'a')
            ->where('a.id = ?1')
            ->setParameter(1, $adsId)
            ->getQuery()
            ->execute();

        return new JsonModel(array($adsId));

    }

    public function getAdsPreviewAction()
    {
        $userId = $this->params('userId');

        $ads = $this->em()->createQueryBuilder()
            ->select('a.id, a.title')
            ->from('Application\Entity\Ads', 'a')
            ->where('a.userid = ?1')
            ->setParameter(1, $userId)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        return new JsonModel(array(
            'adsPreview' => $ads
        ));
    }

    public function getAdsAction()
    {
        $adsId = $this->params('adsId');

        $ads = $this->em()->createQueryBuilder()
            ->select(array('a', 'r', 'city', 'currency'))
            ->from('Application\Entity\Ads', 'a')
            ->leftjoin('a.regionid', 'r')
            ->leftjoin('a.cityid', 'city')
            ->leftjoin('a.currencyid', 'currency')
            ->where('a.id = ?1')
            ->orderBy('a.created')
            ->setParameter(1, $adsId)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $props = $this->em()->createQueryBuilder()
            ->select(array('av', 'val', 'attr'))
            ->from('Application\Entity\AdsValues', 'av')
            ->join('av.adsid', 'a', 'WITH', 'av.adsid=?1')
            ->join('av.valueid', 'val')
            ->join('val.attrid', 'attr')
            ->setParameter(1, $adsId)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $propsGroups = array();

        foreach ($props as $row => $prop) {
            $attrName = $prop['valueid']['attrid']['name'];
            $propsGroups[$attrName][] = $prop['valueid']['value'];
        }

        return new JsonModel(array(
            'ads' => !empty($ads) ? $ads[0] : null,
            'adsProps' => $propsGroups
        ));
    }
}