<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 25.04.15
 * Time: 18:48
 */

namespace Application\EntityRepository;


use Doctrine\ORM\EntityRepository;

class CategoryAttributesRepository extends EntityRepository
{
    public function getAttrsByCategoryId($categoryId)
    {
        $query = $this->_em
            ->createQuery("Select catattr from Application\Entity\CategoryAttributes catattr
                                            WHERE catattr.catid=:categoryId")
            ->setParameters(array(
                'categoryId' => $categoryId,
            ));

        return $query;
    }
}