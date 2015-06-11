<?php

namespace Admin\Controller;


use Doctrine\ORM\Query;
use User\Service\ElasticSearchService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class UsersController extends AbstractActionController
{
    protected $em;
    protected $elasticUserSearch;

    public function usersAction()
    {
        return new ViewModel();
    }

    public function getUsersAction()
    {
        $request = $this->getRequest();
        $data = $request->getQuery()->toArray();
        $em = $this->getEntityManager();

        if (!empty($data['search'])) {
            return $this->searchAction();
        }

        $users = $em->createQueryBuilder()
            ->select('u.id, u.role, u.stat, u.email')
            ->from('Application\Entity\Users', 'u')
            ->setFirstResult($data['offset'])
            ->setMaxResults($data['limit'])
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        return new JsonModel(array(
            'total' => sizeof($users),
            'rows' => $users
        ));
    }

    public function updateAction()
    {
        $request = $this->getRequest();
        $elasticService = $this->getElasticSearchService();

        if ($request->isPost()) {
            $data = $request->getPost()->toArray();
            $em = $this->getEntityManager();

            $user = $em->find('Application\Entity\Users', $data['id']);

            $em->createQueryBuilder()
                ->update('Application\Entity\Users', 'u')
                ->set("u.{$data['name']}", '?1')
                ->where('u.id = ?2')
                ->setParameter(1, $data['value'])
                ->setParameter(2, $data['id'])
                ->getQuery()
                ->execute();

            $elasticService->updateUser($user);

            return new JsonModel(array(1));
        }
    }

    public function searchAction()
    {
        $request = $this->getRequest();
        $data = $request->getQuery()->toArray();
        $em = $this->getEntityManager();
        $elasticService = $this->getElasticSearchService();

        $ids = $elasticService->searchUser($data, $data['offset'], $data['limit']);

        $users = $em->createQueryBuilder()
            ->select('u.role, u.email, u.stat, u.id')
            ->from('Application\Entity\Users', 'u')
            ->andWhere('u.id IN (?1)')
            ->setFirstResult($data['offset'])
            ->setMaxResults($data['limit'])
            ->setParameter(1, $ids)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        return new JsonModel(array(
            'total' => sizeof($users),
            'rows' => $users
        ));

    }

    public function setElasticSearchService(ElasticSearchService $service)
    {
        $this->elasticUserSearch = $service;

        return $this;
    }

    public function getElasticSearchService()
    {
        if (!$this->elasticUserSearch) {
            $this->elasticUserSearch = $this->getServiceLocator()->get('User\Service\Search');
        }
        return $this->elasticUserSearch;
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;

        return $this;
    }

    public function getEntityManager()
    {
        if (!$this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }
}