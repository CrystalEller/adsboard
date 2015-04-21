<?php

namespace Ads\Service;


use Doctrine\ORM\EntityManager;

class AdsSelectorService
{

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function countAdsByCategories($ids = null)
    {
        if (!empty($ids)) {
            $number = $this->em
                ->createQuery("SELECT COUNT(e.id) FROM Application\Entity\Ads e
                                                                    WHERE e.categoryid IN (:catsId)")
                ->setParameters(array(
                    'catsId' => $ids,
                ))
                ->getSingleScalarResult();
        } else {
            $number = $this->em
                ->createQuery("SELECT COUNT(e.id) FROM Application\Entity\Ads e")
                ->getSingleScalarResult();
        }

        return $number;
    }

    public function getAdsByCategories($page, array $categoriesId = null, $limit = 10)
    {
        $offset = ($page == 0) ? 0 : ($page - 1) * $limit;

        if (!empty($categoriesId)) {
            $ads = $this->em
                ->createQuery("Select c from Application\Entity\Ads c
                                          WHERE c.categoryid IN (:catsId) ORDER BY c.created DESC")
                ->setParameters(array(
                    'catsId' => $categoriesId,
                ))
                ->setMaxResults($limit)
                ->setFirstResult($offset)
                ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        } else {
            $ads = $this->em
                ->createQuery("Select c from Application\Entity\Ads c ORDER BY c.created DESC")
                ->setMaxResults($limit)
                ->setFirstResult($offset)
                ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }
        return $ads;
    }

    public function getAttrValuesByCategory($categoryId)
    {

        $attrValues = $this->em
            ->createQuery("Select attr.name, val.value from Application\Entity\AdsValues val
                                            JOIN val.attrid attr
                                            WHERE attr.catid=:categoryId")
            ->setParameters(array(
                'categoryId' => $categoryId,
            ))
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return $attrValues;
    }
}