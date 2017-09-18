<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 10.08.17
 */

namespace NewInventor\PropertyBag\Normalizer;


use NewInventor\TypeChecker\TypeChecker;

class IntNormalizer extends AbstractNormalizer
{
    protected function validateInputTypes($value)
    {
        TypeChecker::check($value)->tnull()->tnumeric()->fail();
    }
    
    protected function normalizeInputValue($value)
    {
        return (int)$value;
    }
}