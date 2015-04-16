<?php

namespace FormBuilder\Validator;


use Zend\Validator\Exception;
use Zend\Validator\InArray;
use Zend\Validator\AbstractValidator;

class Checkboxes extends AbstractValidator
{

    const VALUE_NOT_EXIST = "notExist";
    const INPUT_ERROR = "inputError";

    protected $data;

    protected $messageTemplates = array(
        self::VALUE_NOT_EXIST => "'%value%' не существует",
        self::INPUT_ERROR => "ошибка ввода"
    );

    public function __construct($data)
    {

        if (!is_array($data['values'])) {
            throw new Exception\InvalidArgumentException(
                'Option "values" is required and must be an array'
            );
        }

        $this->data = $data;
    }

    public function isValid($value)
    {
        $validator = new InArray();
        $validator->setHaystack(array($this->data['values']));

        if (is_array($value)) {
            foreach ($value as $val) {
                if (!$validator->isValid($val)) {
                    $this->error(self::VALUE_NOT_EXIST, $val);
                    return false;
                }
            }
            return true;
        }

        $this->error(self::INPUT_ERROR);
        return false;
    }

}