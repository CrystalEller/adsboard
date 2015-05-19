<?php

namespace FormBuilder\Validator\FormValidator;


use Doctrine\Common\Persistence\ObjectRepository;
use FormBuilder\Factory\ValidatorFactory;
use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;
use Doctrine\ORM\Query;

class FormBuilder extends AbstractValidator
{

    const VALUE_NOT_EXIST = 'notExist';
    const INPUT_ERROR = 'Input Error';
    protected $attrRepository;
    protected $valRepository;
    private $messageBuffer = array();

    public function __construct($options)
    {

        if (!isset($options['attr_repository']) || !$options['attr_repository'] instanceof ObjectRepository) {
            if (!array_key_exists('attr_repository', $options)) {
                $provided = 'nothing';
            } else {
                if (is_object($options['attr_repository'])) {
                    $provided = get_class($options['attr_repository']);
                } else {
                    $provided = getType($options['attr_repository']);
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

        $this->attrRepository = $options['attr_repository'];
        $this->valRepository = $options['val_repository'];

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

        $catIds = preg_split('/\./', $catIp);
        $numberOfCats = sizeof($catIds);

        $catProps = $this->attrRepository->findBy(array('catid' => $catIds[$numberOfCats - 1]));

        if (!sizeof($catProps)) {
            return true;
        }

        if (is_array($props)) {
            foreach ($props as $key => $value) {
                $match = $this->valRepository
                    ->getValues($key, intval($catIds[$numberOfCats - 1]), 'admin')
                    ->getResult(Query::HYDRATE_ARRAY);

                $data = $this->attrRepository
                    ->findOneBy(array('id' => $key, 'catid' => $catIds[$numberOfCats - 1]))
                    ->toArray();

                if (!empty($data)) {
                    $type = $data['values']['type'];

                    if (!empty($match)) {
                        foreach ($match as $val) {
                            $data['values']['values'][] = strval($val['id']);
                        }
                    }

                    $validator = ValidatorFactory::create($type, $data['values']);


                    //var_dump($props[$key]);

                    if (!$validator->isValid($props[$key])) {
                        $errors = implode(',', array_keys($validator->getMessages()));
                        $this->messageBuffer[$prefix . '[' . $key . ']'] = $errors;
                    }
                } else {
                    return false;
                }
            }

            return (sizeof($this->getMessages()) > 0) ? false : true;
        }
        return false;
    }

    public function getMessages()
    {
        return $this->messageBuffer;
    }

}