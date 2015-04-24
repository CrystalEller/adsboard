<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 24.04.15
 * Time: 17:09
 */

namespace Application\EntityRepository;


use Doctrine\ORM\EntityRepository;

class CurrencyRepository extends EntityRepository
{
    public function getAll()
    {
        $query = $this->_em
            ->createQuery('Select r from Application\Entity\Currency r');

        return $query;
    }
}