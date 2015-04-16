<?php

namespace Ads\Service;


use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AdsSelectorService
{

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getAdsByCategories($page, array $categoriesId = null, $limit = 10)
    {
        $offset = ($page == 0) ? 0 : ($page - 1) * $limit;

        if (!empty($categoriesId) && is_array($categoriesId)) {
            $ads = $this->em()
                ->createQuery("Select c from Application\Entity\Ads c
                                          WHERE c.categoryid IN (:catsId) LIMIT :offset,:limit")
                ->setParameters(array(
                    'catsId' => implode(',', $categoriesId),
                    'offset' => $offset,
                    'limit' => $limit
                ))
                ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        } else {
            $ads = $this->em()
                ->createQuery("Select c from Application\Entity\Ads c
                                          WHERE c.categoryid LIMIT :offset,:limit")
                ->setParameters(array(
                    'offset' => $offset,
                    'limit' => $limit
                ))
                ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }
        return $ads;
    }

    public function getAdsByAttributes($page, array $attributesId, $limit = 10)
    {
        $offset = ($page == 0) ? 0 : ($page - 1) * $limit;


        $ads = $this->em()
            ->createQuery("Select ads from Application\Entity\Ads ads JOIN
                                        Application\Entity\Categories cat WITH
                                        ads.categoryid=cat.id JOIN
                                        Application\Entity\CategoryAttributes attr WITH
                                        attr.catid=cat.id
                                        WHERE c.id IN (:attributesId) LIMIT :offset,:limit")
            ->setParameters(array(
                'attributesId' => implode(',', $attributesId),
                'offset' => $offset,
                'limit' => $limit
            ))
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return $ads;
    }
}