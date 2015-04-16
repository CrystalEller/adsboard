<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 30.03.15
 * Time: 22:17
 */

namespace FormBuilder\Factory;


class ValidatorFactory
{
    public static function create($name, $data)
    {
        $class = '\\FormBuilder\\Validator\\' . $name;
        if (class_exists($class)) {
            return new $class($data);
        } else {
            throw new \Exception("Class " . $name . " doesn't exist");
        }
    }
}