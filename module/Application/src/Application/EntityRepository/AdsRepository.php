<?php

namespace Application\EntityRepository;


use Doctrine\ORM\EntityRepository;

class AdsRepository extends EntityRepository
{

    public function countAdsAll()
    {
        $query = $this->_em
            ->createQuery("SELECT COUNT(e.id) FROM Application\Entity\Ads e");

        return $query;
    }

    public function countAdsByCategories($ids = null)
    {
        if (!empty($ids)) {
            $query = $this->_em
                ->createQuery("SELECT COUNT(e.id) FROM Application\Entity\Ads e
                                                                    WHERE e.categoryid IN (:catsId)")
                ->setParameters(array(
                    'catsId' => $ids,
                ));
        } else {
            $query = $this->countAdsAll();
        }

        return $query;
    }

    public function getAdsByCategories($page, $categoriesId = null, $limit = 10)
    {
        $offset = ($page == 0) ? 0 : ($page - 1) * $limit;

        if (!empty($categoriesId)) {
            $query = $this->_em
                ->createQuery("Select a,cur,r,city,u from Application\Entity\Ads a
                                          LEFT JOIN a.currencyid cur
                                          LEFT JOIN a.regionid r
                                          LEFT JOIN a.cityid city
                                          LEFT JOIN a.userid u
                                          WHERE a.categoryid IN (:catsId) ORDER BY a.created DESC")
                ->setParameters(array(
                    'catsId' => (array)$categoriesId,
                ))
                ->setMaxResults($limit)
                ->setFirstResult($offset);
        } else {
            $query = $this->getAdsAll($page, $limit);
        }
        return $query;
    }

    public function getAdsByUserId($userId, $page, $limit = 10)
    {
        $offset = ($page == 0) ? 0 : ($page - 1) * $limit;

        $query = $this->_em
            ->createQuery("Select a,cur,r,city,u from Application\Entity\Ads a
                                          LEFT JOIN a.currencyid cur
                                          LEFT JOIN a.regionid r
                                          LEFT JOIN a.cityid city
                                          LEFT JOIN a.userid u
                                          WHERE a.userid=:userId ORDER BY a.created DESC")
            ->setParameters(array(
                'userId' => $userId,
            ))
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        return $query;
    }

    public function countAdsByUserId($userId)
    {
        $query = $this->_em
            ->createQuery("SELECT COUNT(e.id) FROM Application\Entity\Ads e
                                              WHERE e.userid=:userId")
            ->setParameters(array(
                'userId' => $userId
            ));

        return $query;
    }

    public function getAdsAll($page, $limit = 10)
    {
        $offset = ($page == 0) ? 0 : ($page - 1) * $limit;

        $query = $this->_em
            ->createQuery("Select a,cur,r,city,u from Application\Entity\Ads a
                                          LEFT JOIN a.currencyid cur
                                          LEFT JOIN a.regionid r
                                          LEFT JOIN a.cityid city
                                          LEFT JOIN a.userid u ORDER BY a.created DESC")
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        return $query;
    }

}