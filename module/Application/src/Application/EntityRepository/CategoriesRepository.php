<?php

namespace Application\EntityRepository;


use Doctrine\ORM\EntityRepository;

class CategoriesRepository extends EntityRepository
{
    public function getRootCategories()
    {
        $query = $this->_em
            ->createQuery('Select c.id, c.name from Application\Entity\Categories c WHERE c.id=c.root');

        return $query;
    }


}