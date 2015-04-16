<?php

namespace Ads\Filter;


use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class PriceFilter extends InputFilter implements InputFilterInterface
{

    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->add(array(
            'name' => 'price',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                )
            ),
            'validators' => array(
                array(
                    'name' => 'Digits',
                    'break_chain_on_failure' => true
                ),
                array(
                    'name' => 'Between',
                    'options' => array(
                        'min' => 1,
                        'max' => 10000000,
                        'inclusive' => false
                    ),
                )
            )
        ));

        $this->add(array(
            'name' => 'no-price',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'Identical',
                    'options' => array(
                        'token' => 'no-price'
                    ),
                ),
            )
        ));

        $this->add(array(
            'name' => 'currency',
            'required' => true,
            'validators' => array(
                array(
                    'name' => '\DoctrineModule\Validator\ObjectExists',
                    'options' => array(
                        'object_repository' => $em->getRepository('\Application\Entity\Currency'),
                        'fields' => 'id',
                        'messages' => array(
                            'noObjectFound' => 'Данная валюта не существует!'
                        ),
                    )
                )
            )
        ));
    }

}