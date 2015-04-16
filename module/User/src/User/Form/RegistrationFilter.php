<?php

namespace User\Form;

use Zend\InputFilter\InputFilter;

class RegistrationFilter extends InputFilter
{

    protected $em;

    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;

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
                ),
                array(
                    'name' => '\DoctrineModule\Validator\NoObjectExists',
                    'options' => array(
                        'object_repository' => $this->em->getRepository('\Application\Entity\Users'),
                        'fields' => 'email',
                        'messages' => array(
                            'objectFound' => 'Этот email уже существует!'
                        ),
                    ),
                )
            )
        ));

        $this->add(array(
            'name' => 'password1',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 6,
                        'max' => 30,
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name' => 'password2',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'Identical',
                    'options' => array(
                        'token' => 'password1',
                    ),
                ),
            ),
        ));
    }

}