<?php

namespace Ads\Filter;


use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class AdsFilter extends InputFilter implements InputFilterInterface
{

    public function __construct(\Doctrine\ORM\EntityManager $em, \DoctrineExtensions\NestedSet\Manager $nsm)
    {

        $this->add(array(
            'name' => 'region',
            'required' => true,
            'validators' => array(
                array(
                    'name' => '\DoctrineModule\Validator\ObjectExists',
                    'options' => array(
                        'object_repository' => $em->getRepository('\Application\Entity\Region'),
                        'fields' => 'id',
                        'messages' => array(
                            'noObjectFound' => 'Даная область не существует!'
                        ),
                    )
                )
            )
        ));

        $this->add(array(
            'name' => 'city',
            'required' => true,
            'validators' => array(
                array(
                    'name' => '\DoctrineModule\Validator\ObjectExists',
                    'options' => array(
                        'object_repository' => $em->getRepository('\Application\Entity\City'),
                        'fields' => 'id',
                        'messages' => array(
                            'noObjectFound' => 'Данный город не существует!'
                        ),
                    )
                )
            )
        ));

        $this->add(array(
            'name' => 'category',
            'required' => true,
            'validators' => array(
                array(
                    'name' => '\Ads\Validator\CategoryValidator',
                    'options' => array(
                        'object_repository' => $em->getRepository('\Application\Entity\Categories'),
                        'nsm' => $nsm,
                        'fields' => 'id',
                        'messages' => array(
                            'noObjectFound' => 'Данная категория не существует!'
                        ),
                    )
                ),
            )
        ));


        $this->add(array(
            'name' => 'title',
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
                        'max' => 40,
                    ),
                )
            )
        ));

        $this->add(array(
            'name' => 'description',
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
                        'min' => 50,
                        'max' => 300,
                    ),
                )
            )
        ));


        $this->add(array(
            'name' => 'files',
            'type' => 'Zend\InputFilter\FileInput',
            'required' => false,
            'validators' => array(
                array(
                    'name' => '\Zend\Validator\File\Extension',
                    'options' => array(
                        array('jpeg', 'jpg', 'png')
                    )
                ),
                array(
                    'name' => '\Zend\Validator\File\Size',
                    'options' => array(
                        array('max' => '5MB')
                    )
                ),
                array(
                    'name' => '\Zend\Validator\File\UploadFile',
                )
            )
        ));


    }
}