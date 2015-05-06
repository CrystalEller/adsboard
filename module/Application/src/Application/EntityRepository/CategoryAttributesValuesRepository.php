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
    public function getValuesByAttrId($attrId, $owner)
    {
        $query = $this->_em
            ->createQuery("Select av.id, av.value from Application\Entity\CategoryAttributesValues av
                                                WHERE av.attrid=:attrId AND av.owner=:owner")
            ->setParameters(array(
                'attrId' => $attrId,
                'owner' => $owner
            ));

        return $query;
    }

    public function getValuesByCategoryId($categoryId, $owner)
    {
        $query = $this->_em
            ->createQuery("Select attr.name, av.value, av.id from Application\Entity\CategoryAttributesValues av
                                            JOIN av.attrid attr
                                            WHERE attr.catid=:categoryId
                                            AND av.owner=:owner
                                            ORDER BY attr.name")
            ->setParameters(array(
                'categoryId' => $categoryId,
                'owner' => $owner
            ));

        return $query;
    }

    public function getValues($attrId, $catid, $owner)
    {
        $query = $this->_em
            ->createQuery("Select av from Application\Entity\CategoryAttributesValues av
                                                JOIN av.attrid attr
                                                WHERE attr.id=:attrId AND
                                                      av.owner=:owner AND
                                                      attr.catid=:catid")
            ->setParameters(array(
                'attrId' => $attrId,
                'catid' => $catid,
                'owner' => $owner
            ));

        return $query;
    }

}