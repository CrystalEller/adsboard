<?php

namespace Ads\Service;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;

class SearchService
{

    private $qb;
    private $elastic;
    private $args = array();
    private $ids = array();

    public function __construct(EntityManager $em, ElasticSearchService $elastic)
    {
        $this->elastic = $elastic;
        $this->qb = $em->createQueryBuilder();
        $this->qb->select('ads, adsCur')
            ->from('Application\Entity\Ads', 'ads');
    }

    public function search($page, $limit = 10)
    {
        $offset = ($page == 0) ? 0 : ($page - 1) * $limit;

        $args = $this->getArgs($page, $limit);

        $query = $this->qb
            ->leftJoin('ads.currencyid', 'adsCur');

        if (!empty($args)) {
            $query = $query
                ->add('where', new Expr\Andx($args));
        }

        $query = $query
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery();


        return $query->getResult(Query::HYDRATE_ARRAY);
    }

    public function countAds()
    {
        $args = $this->getArgs();

        $query = $this->qb
            ->resetDQLParts(array('select', 'from'))
            ->select('count(ads.id)')
            ->from('Application\Entity\Ads', 'ads');

        if (!empty($args)) {
            $query = $query
                ->add('where', new Expr\Andx($args));
        }

        $query = $query->getQuery();

        return $query->getSingleScalarResult();
    }

    public function setData($data)
    {
        if (!is_array($data)) {
            throw new \Exception('Variable $data must be an array');
        } else {
            foreach ($data as $key => $val) {
                $method = 'set' . ucfirst($key);
                if (method_exists($this, $method)) {
                    if (is_array($val)) {
                        $val = array_map(function ($value) {
                            return is_numeric($value) ? intval($value) : $value;
                        }, $val);
                    } else {
                        $val = is_numeric($val) ? intval($val) : $val;
                    }
                    $this->$method($val);
                }
            }
        }

        return $this;
    }

    public function setPropId($attrValuesIds)
    {
        $this->ids['propId'] = $attrValuesIds;
        $this->qb
            ->innerJoin('Application\Entity\AdsValues', 'adsVal', 'WITH', 'adsVal.adsid=ads.id')
            ->innerJoin('Application\Entity\CategoryAttributesValues', 'attrVal', 'WITH', 'adsVal.valueid=attrVal.id');
    }

    public function setRegionId($regionIds)
    {
        $this->ids['regionId'] = $regionIds;
    }

    public function setCityId($citiesIds)
    {
        $this->ids['cityId'] = $citiesIds;
    }

    public function setCategoryId($categoryIds)
    {
        $this->ids['categoryId'] = $categoryIds;
    }

    public function setQuery($query)
    {
        $this->ids['query'] = $query;
    }

    private function getArgs()
    {
        foreach ($this->ids as $key => $val) {
            if (!empty($val)) {
                switch ($key) {
                    case 'propId':
                        $this->args[] = $this->qb->expr()->in('attrVal.id', $val);
                        break;
                    case 'regionId':
                        $this->args[] = $this->qb->expr()->in('ads.regionid', $val);
                        break;
                    case 'cityId':
                        $this->args[] = $this->qb->expr()->in('ads.cityid', $val);
                        break;
                    case 'categoryId':
                        $this->args[] = $this->qb->expr()->in('ads.categoryid', $val);
                        break;
                    case 'query':
                        $adsIds = $this->elastic->searchAds($this->ids);
                        if (!empty($adsIds)) {
                            $this->args[] = $this->qb->expr()->in('ads.id', $adsIds);
                        }
                        break;
                }
            }
        }

        return $this->args;
    }
}