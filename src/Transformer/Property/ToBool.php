<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 10.08.17
 */

namespace NewInventor\DataStructure\Transformer\Property;


use NewInventor\TypeChecker\TypeChecker;

class ToBool extends Transformer
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
    
    protected function transformInputValue($value)
    {
        if ($this->true !== [] && in_array($value, $this->true, true)) {
            return true;
        }
        if ($this->false !== [] && in_array($value, $this->false, true)) {
            return false;
        }
    
        return (boolean)$value;
    }
}