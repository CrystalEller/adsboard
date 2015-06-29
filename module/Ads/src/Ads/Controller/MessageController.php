<?php

namespace Ads\Controller;


use Application\Entity\Messages;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class MessageController extends AbstractActionController
{
    protected $em;

    public function getMessagesAction()
    {
        $em = $this->getEntityManager();

        $user = $this->identity();

        $messages = $em->createQueryBuilder()
            ->select('m')
            ->from('Application\Entity\Messages', 'm')
            ->where('m.userid = ?1')
            ->setParameter(1, $user->getId())
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $vm = new ViewModel(
            array(
                'messages' => $messages
            )
        );

        $vm->setTemplate('ads/message/get-messages-' . $user->getRole());
        return $vm;
    }

    public function createAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $em = $this->getEntityManager();
            $data = $request->getPost();
            $msgFilter = $this->getServiceLocator()->get('Ads\Filter\Message');
            $adsId = $this->params()->fromRoute('adsId', 0);

            if ($msgFilter->setData($data)->isValid()) {
                $ads = $em->find('Application\Entity\Ads', $adsId);

                $message = new Messages();

                $message->setAdsid($ads)
                    ->setEmail($data['email'])
                    ->setMessage($data['message']);

                $em->persist($message);
                $em->flush();

                return new JsonModel(
                    array(
                        'success' => true
                    )
                );
            }
        }
    }

    public function deleteAction()
    {
        $id = $this->params()->fromRoute('msgId', 0);

        if ($this->acl()->belongsToUser('message', $id)) {
            $em = $this->getEntityManager();

            $message = $em->find('Application\Entity\Messages', $id);

            $em->remove($message);
            $em->flush();

            return new JsonModel(
                array(
                    'success' => true
                )
            );
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