<?php

namespace User\Controller;

use Zend\Debug\Debug;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Entity\Users;

class UserController extends AbstractActionController
{
    protected $em;

    public function loginAction()
    {
        $request = $this->getRequest();
        $form = new \User\Form\LoginForm();
        $form->setData($request->getPost());

        if ($request->isPost()) {
            $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');

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
        $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        $authService->getStorage()->clear();

        return $this->redirect()->toRoute('home');
    }

    public function confirmRegistrationAction()
    {
        $em = $this->getEntityManager();
        $hash = $this->params('hash');

        $user = $em->getRepository('\Application\Entity\Users')->findOneBy(array('password' => $hash));

        if (empty($user)) {
            $view = new ViewModel();
            $view->setTemplate('application/error/404');
            return $view;
        } else {
            $user->setStat('confirmed');
            $user->setRole('user');

            $em->persist($user);
            $em->flush();
        }

        return array();
    }

    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

    public function registrationAction()
    {

        $request = $this->getRequest();
        $form = $this->getServiceLocator()->get('User\Form\RegistrationForm');


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

                $this->getEntityManager()->persist($user);
                $this->getEntityManager()->flush();


                $view = new ViewModel();
                $view->setTemplate('user/user/email-registration');
                return $view;
            }
        }

        return array('form' => $form);

        /* $form=$this->getServiceLocator()->get('User\Form\RegistrationForm');
         $request = $this->getRequest();
         $response = $this->getResponse();

         $messages = array();
         if ($request->isXmlHttpRequest()){

             $form->setData($request->getPost());
             if ( ! $form->isValid()) {
                 $errors = $form->getMessages();
                 foreach($errors as $key=>$row)
                 {
                     if (!empty($row) && $key != 'submit') {
                         foreach($row as $keyer => $rower)
                         {

                             $messages[$key][] = $rower;
                         }
                     }
                 }
             }

             if (!empty($messages)){
                 $response->setContent(\Zend\Json\Json::encode($messages));
                 return $response;
             } else {
                 $user = new \User\Entity\Users();
                 $bcrypt = new Bcrypt();

                 $password = $bcrypt->create($request->getPost('password1'));
                 $user->setEmail($request->getPost('email'))
                     ->setPassword($password);

                 $this->getEntityManager()->persist($user);
                 $this->getEntityManager()->flush();

                 $this->redirect()->toRoute('home');
             }
         }else{
             return array('form'=>$form);
         }*/
    }
}

