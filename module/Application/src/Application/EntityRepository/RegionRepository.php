<?php

namespace Application\EntityRepository;


use Doctrine\ORM\EntityRepository;

class RegionRepository extends EntityRepository
{
    public function getAll()
    {
        $query = $this->_em
            ->createQuery('Select r from Application\Entity\Region r');

        return $query;
    }
}