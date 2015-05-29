<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 24.05.15
 * Time: 22:34
 */

namespace User\Factory;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RegistrationFormFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm)
    {
        return new \User\Form\RegistrationForm(
            new \User\Form\RegistrationFilter(
                $sm->get('doctrine.entitymanager.orm_default')
            )
        );
    }
}