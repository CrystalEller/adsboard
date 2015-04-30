<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 26.04.15
 * Time: 22:18
 */

namespace Application\EntityRepository;


use Doctrine\ORM\EntityRepository;

class CategoryAttributesValuesRepository extends EntityRepository
{
    public function getValuesByAttrId($attrId)
    {
        $query = $this->_em
            ->createQuery('Select av.id, av.value from Application\Entity\CategoryAttributesValues av
                                                WHERE av.attrid=:attrId')
            ->setParameters(array(
                'attrId' => $attrId,
            ));

        return $query;
    }
}