<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 04.04.15
 * Time: 13:33
 */

namespace Ads\Validator;


use DoctrineModule\Validator\ObjectExists;
use Zend\Validator\Exception;

class CategoryValidator extends ObjectExists
{

    private $messages = array();

    public function __construct($options)
    {
        if (!is_object($options['nsm']) || !($options['nsm'] instanceof \DoctrineExtensions\NestedSet\Manager)) {
            throw new \Exception('nsm must be an instance of \DoctrineExtensions\NestedSet\Manager');
        }
        parent::__construct($options);
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function isValid($value, $context = null)
    {
        $nsm = $this->getOption('nsm');

        if (is_array($value)) {
            foreach ($value as $groupId => $catId) {
                $match = $this->objectRepository->findOneBy(array('id' => $catId));

                if (!is_object($match)) {
                    $this->messages[$groupId] = $this->createMessage(ObjectExists::ERROR_NO_OBJECT_FOUND, '');
                } else {
                    $desc = $nsm->wrapNode($match)->getChildren();

                    if (is_array($desc) && !empty($desc)) {
                        $found = false;
                        foreach ($desc as $cat) {
                            if (array_search($cat->getId(), $value)) {
                                $found = true;
                            }
                        }
                        if (!$found) {
                            $this->messages[$groupId + 1] = $this->createMessage(ObjectExists::ERROR_NO_OBJECT_FOUND, '');
                            return false;
                        }
                    }
                }
            }
            if (sizeof($this->messages) > 0) {
                return false;
            } else {
                return true;
            }
        }
        return false;
    }

}