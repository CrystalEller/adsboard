<?php

namespace Admin\Form;

use Zend\InputFilter\InputFilter;

class CategoryFilter extends InputFilter implements \Zend\InputFilter\InputFilterInterface
{
    public function __construct(\Doctrine\ORM\EntityManager $em)
    {

        $this->add(array(
            'name' => 'name',
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
                        'min' => 5,
                        'max' => 25,
                    ),
                ),
                array(
                    'name' => '\Admin\Validator\CategoryValidator',
                    'options' => array(
                        'object_repository' => $em->getRepository('\Application\Entity\Categories'),
                        'fields' => 'name',
                        'messages' => array(
                            'objectFound' => 'Эта категория уже существует!'
                        ),
                    )
                )
            )
        ));

        $this->add(array(
            'name' => 'id',
            'required' => true,
            'validators' => array(
                array(
                    'name' => '\DoctrineModule\Validator\ObjectExists',
                    'options' => array(
                        'object_repository' => $em->getRepository('\Application\Entity\Categories'),
                        'fields' => 'id',
                    )
                )
            )
        ));

    }
}
