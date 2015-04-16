<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 29.03.15
 * Time: 23:25
 */

namespace FormBuilder\Validator;


use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;
use Zend\Validator\StringLength;

class SingleLineText extends AbstractValidator
{

    protected $data;

    public function __construct($data)
    {

        if (!is_array($data['length'])) {
            throw new Exception\InvalidArgumentException(
                'Option "length" is required and must be an array'
            );
        }

        $this->data = $data;
    }

    public function isValid($value)
    {
        $validator = new StringLength(
            array(
                'min' => $this->data['length']['min'],
                'max' => $this->data['length']['max'],
                'encoding' => 'UTF-8'
            )
        );

        if ($validator->isValid($value)) {
            return true;
        } else {
            $this->error(implode(',', $validator->getMessages()));
            return false;
        }
    }

}