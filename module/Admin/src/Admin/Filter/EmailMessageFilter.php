<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 29.06.15
 * Time: 12:08
 */

namespace Admin\Filter;


use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class EmailMessageFilter extends InputFilter implements InputFilterInterface
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
            'name' => 'subject',
            'required' => false,
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
                        'max' => 50,
                    ),
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
                        'max' => 400,
                    ),
                )
            )
        ));
    }
}