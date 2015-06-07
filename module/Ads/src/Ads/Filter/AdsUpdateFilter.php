<?php

namespace Ads\Filter;


use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class AdsUpdateFilter extends InputFilter implements InputFilterInterface
{
    public function __construct()
    {
        $this->add(array(
            'name' => 'userName',
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
                        'min' => 3,
                        'max' => 15,
                    ),
                )
            )
        ));

        $this->add(array(
            'name' => 'telephone',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                )
            ),
            'validators' => array(
                array(
                    'name' => 'Regex',
                    'options' => array(
                        'pattern' => '/^\d{10}$/'
                    ),
                )
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
                    'name' => 'Zend\Validator\File\Extension',
                    'options' => array(
                        'extension' => 'jpeg,jpg,png'
                    )
                ),
                array(
                    'name' => 'Zend\Validator\File\Size',
                    'options' => array(
                        'max' => '5MB',
                        'min' => '10kB'
                    )
                ),
                array(
                    'name' => 'Ads\Validator\FilesCountValidator',
                    'options' => array(
                        'max' => '9'
                    )
                )
            )
        ));
    }
}