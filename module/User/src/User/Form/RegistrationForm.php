<?php

namespace User\Form;


use Zend\Form\Form;

class RegistrationForm extends Form
{

    protected $captcha;

    public function __construct(\User\Form\RegistrationFilter $rf)
    {
        parent::__construct('registration');
        $this->setAttribute('action', '/registration');
        $this->setAttribute('method', 'post');
        $this->setInputFilter($rf);

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
            'name' => 'password1',
            'attributes' => array(
                'type' => 'password',
                'id' => 'InputPassword1'
            ),
            'options' => array(
                'label' => 'Введите пароль',
            )
        ))->add(array(
            'name' => 'password2',
            'attributes' => array(
                'type' => 'password',
                'id' => 'InputPassword1'
            ),
            'options' => array(
                'label' => 'Подтвердите пароль',
            )
        ))->add(array(
            'name' => 'submit',
            'type' => 'button',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Зарегистрироваться',
                'class' => 'btn btn-default'
            )
        ))->add(array(
            'name' => 'captcha',
            'type' => 'Captcha',
            'attributes' => array(
                'id' => 'captcha',
                'autocomplete' => 'off',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'Please verify you are human',
                'captcha' => new \Zend\Captcha\Image(array(
                    'font' => './public/fonts/arial.ttf',
                    'imgDir' => './data/captcha/images/',
                    'imgUrl' => '/images/captcha/',
                    'width' => '100',
                    'wordLen' => '3'
                ))
            ),
        ));


    }


}