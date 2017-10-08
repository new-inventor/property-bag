<?php
/**
 * Project: property-bag
 * User: george
 * Date: 28.09.17
 */

namespace NewInventor\DataStructure\Transformer;


use NewInventor\TypeChecker\TypeChecker;

class StringToLowerCase extends Transformer
{
    protected function normalizeInputValue($value)
    {
        return strtolower($value);
    }
    
    protected function validateInputTypes($value)
    {
        TypeChecker::check($value)->tstring()->fail();
    }
}