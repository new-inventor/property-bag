<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 12.09.17
 */

namespace NewInventor\PropertyBag\Normalizer;


use NewInventor\TypeChecker\TypeChecker;

class PhoneNormalizer extends RegExpNormalizer
{
    /**
     * PhoneNormalizer constructor.
     */
    public function __construct()
    {
        parent::__construct('/^\+?(?:\d{11}|[-()\d ]{11,21})$/');
    }
    protected function validateInputTypes($value)
    {
        TypeChecker::check($value)->tnull()->tint()->tnumeric()->tstring()->callback(
            function ($value) {
                return is_object($value) && method_exists($value, '__toString');
            }
        )->fail();
    }
    
    protected function normalizeInputValue($value)
    {
        $value = preg_replace('/\D/', '', $value);
    
        return '+' . $value;
    }
}