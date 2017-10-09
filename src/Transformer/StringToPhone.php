<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 12.09.17
 */

namespace NewInventor\DataStructure\Transformer;


use NewInventor\TypeChecker\TypeChecker;

class StringToPhone extends Transformer
{
    protected function transformInputValue($value)
    {
        $value = preg_replace('/\D/', '', $value);
        if (strlen($value) !== 11) {
            throw new \InvalidArgumentException('Phone should be string with 11 numbers');
        }
        
        return '+' . $value;
    }
    
    protected function validateInputTypes($value)
    {
        TypeChecker::check($value)->tstring()->fail();
    }
}