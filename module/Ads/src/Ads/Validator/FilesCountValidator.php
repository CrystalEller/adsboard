<?php

namespace Ads\Validator;


use Zend\Validator\Exception;
use Zend\Validator\File\Count;

class FilesCountValidator extends Count
{
    public function isValid($value, $files = null)
    {
        $this->count = count($files);
        if (($this->getMax() !== null) && ($this->count > $this->getMax())) {
            $this->error(self::TOO_MANY);
            return false;
        }

        if (($this->getMin() !== null) && ($this->count < $this->getMin())) {
            $this->error(self::TOO_FEW);
            return false;
        }

        return true;
    }
}