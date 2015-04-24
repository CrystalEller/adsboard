<?php

namespace Application\EntityRepository;


use Doctrine\ORM\EntityRepository;

class AdsValuesRepository extends EntityRepository
{

    public function getAttrValuesByCategory($categoryId)
    {
        $query = $this->_em
            ->createQuery("Select attr.name, val.value from Application\Entity\AdsValues val
                                            JOIN val.attrid attr
                                            WHERE attr.catid=:categoryId")
            ->setParameters(array(
                'categoryId' => $categoryId,
            ));

        return $query;
    }

}