<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 10.08.17
 */

namespace NewInventor\DataStructure\Transformer;


use NewInventor\TypeChecker\TypeChecker;

class ToString extends Transformer
{
    protected function validateInputTypes($value)
    {
        TypeChecker::check($value)->tnull()->tscalar()->callback(
            function ($value) {
                return is_object($value) && method_exists($value, '__toString');
            }
        )->fail();
    }
    
    protected function transformInputValue($value)
    {
        return (string)$value;
    }
}