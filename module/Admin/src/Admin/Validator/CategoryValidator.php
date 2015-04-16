<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 12.04.15
 * Time: 13:50
 */

namespace Admin\Validator;


use DoctrineModule\Validator\NoObjectExists;
use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;

class CategoryValidator extends NoObjectExists
{

    public function isValid($value, $context = null)
    {
        $value = $this->cleanSearchValue($value);
        $match = $this->objectRepository->findOneBy($value);

        if (is_object($match) && ($context['id'] != $match->getId())) {
            $this->error(self::ERROR_OBJECT_FOUND, $value);

            return false;
        }

        return true;
    }

}