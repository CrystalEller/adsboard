<?php

namespace User\Filter;


use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class MessageFilter extends InputFilter implements InputFilterInterface
{
    public function __construct()
    {
        $this->add(array(
            'name' => 'email',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                )
            ),
            'validators' => array(
                array(
                    'name' => 'EmailAddress',
                )
            )
        ));

        $this->add(array(
            'name' => 'message',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                )
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 20,
                        'max' => 200,
                    ),
                )
            )
        ));
    }
}