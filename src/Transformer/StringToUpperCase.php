<?php
/**
 * Project: property-bag
 * User: george
 * Date: 28.09.17
 */

namespace NewInventor\DataStructure\Transformer;


use NewInventor\TypeChecker\TypeChecker;

class StringToUpperCase extends Transformer
{
    protected function transformInputValue($value)
    {
        return strtoupper($value);
    }
    
    protected function validateInputTypes($value)
    {
        TypeChecker::check($value)->tstring()->fail();
    }
}