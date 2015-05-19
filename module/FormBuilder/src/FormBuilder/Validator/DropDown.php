<?php

namespace FormBuilder\Validator;


use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;
use Zend\Validator\InArray;

class DropDown extends AbstractValidator
{

    const VALUE_NOT_EXIST = "'%value%' не существует";
    const INPUT_ERROR = "Ошибка ввода данных";

    protected $data;

    protected $messageTemplates = array(
        self::VALUE_NOT_EXIST => "'%value%' не существует",
        self::INPUT_ERROR => "ошибка ввода"
    );

    public function __construct($data)
    {

        if (empty($data['values']) || !is_array($data['values'])) {
            throw new Exception\InvalidArgumentException(
                'Option "values" is required and must be an array'
            );
        }

        $this->data = $data;
    }

    public function isValid($value)
    {
        $validator = new InArray();
        $validator->setHaystack($this->data['values']);

        if (is_array($value) && !empty($value[0])) {
            if (!$validator->isValid($value[0])) {
                $this->error(self::VALUE_NOT_EXIST, $value);
                return false;
            }
            return true;
        }

        $this->error(self::INPUT_ERROR);
        return false;
    }

}