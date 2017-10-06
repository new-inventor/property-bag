<?php
/**
 * Project: property-bag
 * User: george
 * Date: 05.10.17
 */

namespace NewInventor\DataStructure\Transformer\Property;


use NewInventor\DataStructure\Loadable;
use NewInventor\TypeChecker\TypeChecker;

class ArrayToLoadable extends Transformer
{
    /**
     * @var string
     */
    protected $class;
    /** @var int */
    protected $strategy;
    
    /**
     * PropertyBagNormalizer constructor.
     *
     * @param string $class
     * @param int    $strategy
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function __construct(string $class, $strategy = Loadable::STRATEGY_STRICT)
    {
        if (!class_exists($class)) {
            throw new \RuntimeException("Class {$class} not found.");
        }
        if (!in_array(Loadable::class, class_implements($class), true)) {
            throw new \InvalidArgumentException('Class must implement ' . Loadable::class);
        }
        $this->class = $class;
        $this->strategy = $strategy;
    }
    
    protected function validateInputTypes($value)
    {
        TypeChecker::check($value)->tarray()->fail();
    }
    
    protected function transformInputValue($value)
    {
        /** @var Loadable $obj */
        $class = new \ReflectionClass($this->class);
        $obj = $class->newInstanceWithoutConstructor();
        $obj->load($value, $this->strategy);
        
        return $obj;
    }
}