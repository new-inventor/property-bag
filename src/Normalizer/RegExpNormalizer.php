<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 12.09.17
 */

namespace NewInventor\PropertyBag\Normalizer;


use NewInventor\PropertyBag\Exception\NormalizeException;
use NewInventor\TypeChecker\TypeChecker;

class RegExpNormalizer extends AbstractNormalizer
{
    /** @var string */
    private $regExp;
    
    /**
     * RegExpNormalizer constructor.
     *
     * @param $regExp
     */
    public function __construct(string $regExp)
    {
        $this->regExp = $regExp;
    }
    
    protected function validateInputTypes($value)
    {
        TypeChecker::check($value)->tnull()->tstring()->callback(
            function ($value) {
                return is_object($value) && method_exists($value, '__toString');
            }
        )->fail();
    }
    
    protected function parseInputValue($value)
    {
        $value = (string)$value;
        if (preg_match($this->regExp, $value, $matches)) {
            return $matches[0];
        }
        
        throw new NormalizeException(static::class, $value);
    }
}