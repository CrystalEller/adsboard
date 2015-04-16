<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 24.03.15
 * Time: 13:30
 */

namespace Application\ControllerPlugin;


use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class EntityManagerPlugin extends AbstractPlugin
{
    public function __invoke()
    {
        return $this->getController()->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    }
}