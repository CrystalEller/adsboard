<?php

namespace Admin\Controller;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class MessageController extends AbstractActionController
{
    protected $em;

    public function messagesAction()
    {
        return new ViewModel();
    }

    public function getAction()
    {
        $em = $this->getEntityManager();
        $data = $this->getRequest()->getQuery();
        $user = $this->identity();

        $sortBy = in_array($data['sort'], array('message,email'), true) ? $data['sort'] : 'created';
        $sortDir = $data['order'] == 'ASC' ? 'ASC' : 'DESC';

        $msgs = $em->createQueryBuilder()
            ->select('m')
            ->from('Application\Entity\Messages', 'm')
            ->where('m.userid = ?1 OR m.userid IS NULL')
            ->orderBy('m.' . $sortBy, $sortDir)
            ->setParameters(
                array(
                    1 => $user->getId(),
                )
            )
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        return new JsonModel(
            array(
                'total' => sizeof($msgs),
                'rows' => $msgs
            )
        );
    }

    public function deleteAction()
    {
        $id = $this->params()->fromRoute('msgId', 0);

        $em = $this->getEntityManager();

        $message = $em->find('Application\Entity\Messages', $id);

        $em->remove($message);
        $em->flush();

        return new JsonModel(
            array(
                'success' => true,
                'id' => $id
            )
        );

    }

    public function answerAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = $request->getPost();
            $filter = $this->getServiceLocator()->get('Admin\Filter\EmailMessage');

            $filter->setData($data);

            if($filter->isValid()) {
                $this->email(array(
                    'to' => $data['email'],
                    'subject' => $data['subject'],
                    'template' => 'admin/message/email',
                ), array(
                    'text' => $data['message'],
                ));

                return new JsonModel(
                    array(
                        'success' => true,
                    )
                );
            }
        }
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