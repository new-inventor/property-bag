<?php
/**
 * Project: property-bag
 * User: george
 * Date: 28.09.17
 */

namespace NewInventor\DataStructure\Transformer;


use NewInventor\TypeChecker\TypeChecker;

class StringToScreamingCase extends Transformer
{
    protected function transformInputValue($value)
    {
        $value = preg_replace(['/[^_a-zA-Z0-9]+/', '/(?:_$|^_)/'], ['_', ''], $value);
        $value = preg_replace_callback('/_?(?:[A-Z]+|\d+)/', function ($matches) {
            return '_' . $matches[0];
        }, $value);
        $value = preg_replace(['/_+/', '/(?:^_+|_+$)/'], ['_', ''], $value);
        return strtoupper($value);
    }
    
    protected function validateInputTypes($value)
    {
        TypeChecker::check($value)->tstring()->fail();
    }
}