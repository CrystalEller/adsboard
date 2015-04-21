<?php

namespace Ads\Service;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr;

class SearchService
{

    private $qb;
    private $args = array();
    private $ids = array();

    public function __construct(EntityManager $em)
    {
        $this->qb = $em->createQueryBuilder();
    }

    public function search($page, $limit = 10)
    {
        $offset = ($page == 0) ? 0 : ($page - 1) * $limit;

        $args = $this->getArgs();

        $query = $this->qb->select('ads')
            ->from('Application\Entity\Ads', 'ads')
            ->add('where', new Expr\Andx($args))
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery();

        return $query->getArrayResult();

    }

    public function countAds()
    {
        $args = $this->getArgs();

        $query = $this->qb->select('count(ads.id)')
            ->from('Application\Entity\Ads', 'ads')
            ->add('where', new Expr\Andx($args))
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    public function setData($data)
    {
        if (!is_array($data)) {
            throw new \Exception('Variable $data must be an array');
        } else {
            foreach ($data as $key => $val) {
                $method = 'set' . $key;
                if (method_exists($this, $method)) {
                    $val = array_map(function ($value) {
                        return is_numeric($value) ? intval($value) : 0;
                    }, $val);
                    $this->$method($val);
                }
            }
        }

        return $this;
    }

    public function setAttrValuesIds($attrValuesIds)
    {
        $this->ids['AttrValuesIds'] = $attrValuesIds;
        $this->qb->innerJoin('Application\Entity\AdsValues', 'adsVal', 'WITH', 'adsVal.adsid=ads.id');
    }

    public function setRegionsIds($regionIds)
    {
        $this->ids['RegionsIds'] = $regionIds;
    }

    public function setCitiesIds($citiesIds)
    {
        $this->ids['CitiesIds'] = $citiesIds;
    }

    private function getArgs()
    {
        foreach ($this->ids as $key => $val) {
            if (!empty($val)) {
                switch ($key) {
                    case 'AttrValuesIds':
                        $this->args[] = $this->qb->expr()->in('adsVal.adsid', $val);
                        break;
                    case 'RegionsIds':
                        $this->args[] = $this->qb->expr()->in('ads.regionid', $val);
                        break;
                    case 'CitiesIds':
                        $this->args[] = $this->qb->expr()->in('ads.cityid', $val);
                        break;
                }
            }
        }

        return $this->args;
    }
}