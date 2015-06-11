<?php

namespace User\Controller;

use Doctrine\ORM\EntityManager;
use User\Form\RegistrationForm;
use User\Service\ElasticSearchService;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Entity\Users;

class UserController extends AbstractActionController
{
    protected $em;
    protected $registrationForm;
    protected $authService;
    protected $elasticUserSearch;

    public function loginAction()
    {
        $request = $this->getRequest();
        $form = new \User\Form\LoginForm();
        $form->setData($request->getPost());

        if ($request->isPost()) {
            $authService = $this->getAuthService();

            $adapter = $authService->getAdapter();
            $adapter->setIdentityValue($request->getPost('email'));
            $adapter->setCredentialValue($request->getPost('password'));
            $authResult = $authService->authenticate();

            if ($authResult->isValid()) {
                $authService->getStorage()->write($authResult->getIdentity());
                return $this->redirect()->toRoute('home');
            } else {
                return array('error' => 'Неверный логин или пароль', 'form' => $form);
            }

        }

        return array('form' => $form);
    }

    public function logoutAction()
    {
        $authService = $this->getAuthService();
        $authService->getStorage()->clear();

        return $this->redirect()->toRoute('home');
    }

    public function confirmRegistrationAction()
    {
        $em = $this->getEntityManager();
        $elasticService = $this->getElasticSearchService();
        $hash = $this->params('hash');

        $user = $em->getRepository('\Application\Entity\Users')->findOneBy(array('password' => $hash));

        if (empty($user)) {
            $view = new ViewModel();
            $view->setTemplate('application/error/404');
            return $view;
        } else {
            $user->setStat('confirmed');
            $user->setRole('user');

            $user = $em->merge($user);
            $em->flush();

            $elasticService->updateUser($user);
        }

        return array();
    }

    public function registrationAction()
    {
        $request = $this->getRequest();
        $form = $this->getRegistrationForm();
        $em = $this->getEntityManager();
        $elasticService = $this->getElasticSearchService();

        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $user = new Users();

                $user->setEmail($request->getPost('email'))
                    ->setPassword($request->getPost('password1'))
                    ->hashPassword();

                $this->email(array(
                    'to' => $request->getPost('email'),
                    'subject' => 'Активация аккаунта',
                    'template' => 'user/mail/email-registration',
                ), array(
                    'hash' => $user->getPassword(),
                ));

                $em->persist($user);
                $em->flush();

                $elasticService->saveUser($user);

                $view = new ViewModel();
                $view->setTemplate('user/user/email-registration');
                return $view;
            }
        }

        return array('form' => $form);
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

    public function setAuthService(AuthenticationService $authService)
    {
        $this->authService = $authService;

        return $this;
    }

    public function getAuthService()
    {
        if (!$this->authService) {
            $this->authService = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
        }
        return $this->authService;
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

    public function setRegistrationForm(RegistrationForm $registrationForm)
    {
        $this->registrationForm = $registrationForm;

        return $this;
    }

    public function getRegistrationForm()
    {
        if (!$this->registrationForm) {
            $this->registrationForm = $this->getServiceLocator()->get('User\Form\RegistrationForm');
        }
        return $this->registrationForm;
    }
}

