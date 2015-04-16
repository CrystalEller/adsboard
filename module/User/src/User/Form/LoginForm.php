<?php

namespace User\Form;

use Zend\Form\Form;

class LoginForm extends Form
{

    public function __construct()
    {
        parent::__construct('registration');
        $this->setAttribute('action', '/login');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type' => 'email',
                'id' => 'InputEmail1'
            ),
            'options' => array(
                'label' => 'Введите email'
            )
        ))->add(array(
            'name' => 'password',
            'attributes' => array(
                'type' => 'password',
                'id' => 'InputPassword1'
            ),
            'options' => array(
                'label' => 'Введите пароль',
            )
        ))->add(array(
            'name' => 'submit',
            'type' => 'button',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Зарегистрироваться',
                'class' => 'btn btn-default'
            )
        ));

    }
}