<?php

namespace FormBuilder\Validator\FormValidator;


use Doctrine\Common\Persistence\ObjectRepository;
use FormBuilder\Factory\ValidatorFactory;
use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;

class FormBuilder extends AbstractValidator
{

    const VALUE_NOT_EXIST = 'notExist';
    const INPUT_ERROR = 'Input Error';
    protected $objectRepository;
    private $messageBuffer = array();

    public function __construct($options)
    {

        if (!isset($options['object_repository']) || !$options['object_repository'] instanceof ObjectRepository) {
            if (!array_key_exists('object_repository', $options)) {
                $provided = 'nothing';
            } else {
                if (is_object($options['object_repository'])) {
                    $provided = get_class($options['object_repository']);
                } else {
                    $provided = getType($options['object_repository']);
                }
            }

            throw new Exception\InvalidArgumentException(
                sprintf(
                    'Option "object_repository" is required and must be an instance of'
                    . ' Doctrine\Common\Persistence\ObjectRepository, %s given',
                    $provided
                )
            );
        }

        $this->objectRepository = $options['object_repository'];

        if (!isset($options['category_ip']) && is_string($options['category_ip'])) {
            throw new Exception\InvalidArgumentException(
                'Option "category_ip" is required and must be a string'
            );
        }

        if (!isset($options['html_name_prefix'])) {
            throw new Exception\InvalidArgumentException(
                'Option "html_name_prefix" is required'
            );
        }

        parent::__construct($options);
    }

    public function isValid($props, $context = null)
    {
        $prefix = $this->getOption('html_name_prefix');
        $catIp = $this->getOption('category_ip');

        $catProps = $this->objectRepository->findBy(array('catid' => substr($catIp, -1)));
        $catCounter = 0;

        if (!sizeof($catProps)) {
            return false;
        }

        if (is_array($props)) {
            foreach ($props as $key => $value) {
                $match = $this->objectRepository
                    ->findOneBy(array('id' => $key, 'catid' => substr($catIp, -1)));

                if (is_object($match)) {
                    $data = unserialize($match->getValues());
                    $validator = ValidatorFactory::create($data['type'], $data);

                    if (!$validator->isValid($props[$key])) {
                        $errors = implode(',', array_keys($validator->getMessages()));
                        $this->messageBuffer[$prefix . '[' . $key . ']'] = $errors;
                    } else {
                        $catCounter++;
                    }
                } else {
                    return false;
                }
            }

            return (sizeof($this->getMessages()) > 0) &&
            ($catCounter === sizeof($catProps)) ? false : true;
        }
        return false;
    }

    public function getMessages()
    {
        return $this->messageBuffer;
    }

}