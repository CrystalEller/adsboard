<?php

namespace Application\EntityRepository;


use Doctrine\ORM\EntityRepository;

class CityRepository extends EntityRepository
{
    public function getCityByRegionId($regionId)
    {
        $query = $this->_em
            ->createQuery('Select c from Application\Entity\City c WHERE c.regionid=:regionid')
            ->setParameters(array('regionid' => $regionId));

        return $query;
    }
}