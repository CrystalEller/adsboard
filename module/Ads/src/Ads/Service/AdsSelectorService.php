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

    public function countAdsByAttributes($ids = null)
    {
        if (!empty($ids)) {
            $number = $this->em
                ->createQuery("Select COUNT(ads.id) from Application\Entity\Ads ads JOIN
                                        Application\Entity\Categories cat WITH
                                        ads.categoryid=cat.id JOIN
                                        Application\Entity\CategoryAttributes attr WITH
                                        attr.catid=cat.id
                                        WHERE attr.id IN (:attributesId)")
                ->setParameters(array(
                    'attributesId' => $ids,
                ))
                ->getSingleScalarResult();
        } else {
            $number = $this->em
                ->createQuery("Select COUNT(ads.id) from Application\Entity\Ads ads JOIN
                                        Application\Entity\Categories cat WITH
                                        ads.categoryid=cat.id JOIN
                                        Application\Entity\CategoryAttributes attr WITH
                                        attr.catid=cat.id")
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

    public function getAdsByAttributes($page, array $attributesId, $limit = 10)
    {
        $offset = ($page == 0) ? 0 : ($page - 1) * $limit;

        $ads = $this->em
            ->createQuery("Select ads from Application\Entity\Ads ads JOIN
                                        Application\Entity\Categories cat WITH
                                        ads.categoryid=cat.id JOIN
                                        Application\Entity\CategoryAttributes attr WITH
                                        attr.catid=cat.id
                                        WHERE attr.id IN (:attributesId)")
            ->setParameters(array(
                'attributesId' => $attributesId,
            ))
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return $ads;
    }
}