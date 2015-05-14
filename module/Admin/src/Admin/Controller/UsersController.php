<?php

namespace Admin\Controller;


use Doctrine\ORM\Query;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class UsersController extends AbstractActionController
{
    public function usersAction()
    {
        return new ViewModel();
    }

    public function getUsersAction()
    {
        $request = $this->getRequest();
        $data = $request->getQuery()->toArray();

        $users = $this->em()->createQueryBuilder()
            ->select('u')
            ->from('Application\Entity\Users', 'u')
            ->setFirstResult($data['offset'])
            ->setMaxResults($data['limit'])
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        return new JsonModel(array(
            'rows' => $users
        ));
    }

    public function updateAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = $request->getPost()->toArray();

            $this->em()->createQueryBuilder()
                ->update('Application\Entity\Users', 'u')
                ->set("u.{$data['name']}", '?1')
                ->where('u.id = ?2')
                ->setParameter(1, $data['value'])
                ->setParameter(2, $data['pk'])
                ->getQuery()
                ->execute();

            return new JsonModel(array(1));
        }
    }
}