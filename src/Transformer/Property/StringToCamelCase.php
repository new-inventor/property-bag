<?php
/**
 * Project: property-bag
 * User: george
 * Date: 28.09.17
 */

namespace NewInventor\DataStructure\Transformer\Property;


use NewInventor\TypeChecker\TypeChecker;

class StringToCamelCase extends Transformer
{
    protected function transformInputValue($value)
    {
        $value = preg_replace_callback(
            '/[^a-zA-Z0-9]+([a-zA-Z])/',
            function ($matches) {
                return strtoupper($matches[1]);
            },
            $value
        );
        
        return preg_replace('/[^a-zA-Z0-9]/', '', $value);
    }
    
    protected function validateInputTypes($value)
    {
        TypeChecker::check($value)->tstring()->fail();
    }
}