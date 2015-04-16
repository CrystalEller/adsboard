<?php

namespace Admin\Controller;

use Zend\Debug\Debug;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AdminController extends AbstractActionController
{
    protected $em;

    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

    public function indexAction()
    {
        return new ViewModel();
    }

    public function usersAction()
    {
        /*        $request = $this->getRequest();
                $response = $this->getResponse();

                if ($request->isXmlHttpRequest()) {
                    $user = $em->getRepository('\Application\Entity\Users')->
                }*/

        return new ViewModel();

    }
}
