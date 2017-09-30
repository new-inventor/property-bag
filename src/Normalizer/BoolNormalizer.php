<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 10.08.17
 */

namespace NewInventor\PropertyBag\Normalizer;


use NewInventor\TypeChecker\TypeChecker;

class BoolNormalizer extends AbstractNormalizer
{
    /** @var array */
    private $true;
    /** @var array */
    private $false;
    
    /**
     * BoolNormalizer constructor.
     *
     * @param array $true
     * @param array $false
     *
     * @throws \NewInventor\TypeChecker\Exception\TypeException
     */
    public function __construct(array $true = [], array $false = [])
    {
        TypeChecker::check($true)->tarray()->inner()->tscalar()->callback(
            function ($value) {
                return is_object($value) && method_exists($value, '__toString');
            }
        )->fail();
        TypeChecker::check($false)->tarray()->inner()->tscalar()->callback(
            function ($value) {
                return is_object($value) && method_exists($value, '__toString');
            }
        )->fail();
        $this->true = $true;
        $this->false = $false;
    }
    
    protected function parseInputValue($value)
    {
        $value = parent::parseInputValue($value);
        if ($this->true !== [] && in_array($value, $this->true, true)) {
            return true;
        }
        if ($this->false !== [] && in_array($value, $this->false, true)) {
            return false;
        }
    
        return $value;
    }
    
    protected function normalizeInputValue($value)
    {
        return (boolean)$value;
    }
    
    public static function asString(...$config): string
    {
        $values = [];
        if(isset($config[0])) {
            foreach ($config[0] as $trueVal) {
                $values[] = (string)$trueVal;
            }
        }
        if(isset($config[1])) {
            foreach ($config[1] as $falseVal) {
                $values[] = (string)$falseVal;
            }
        }
        return parent::asString() . '_' . implode('_', $values);
    }
    
    public function __toString(): string
    {
        return static::asString($this->true, $this->false);
    }
}