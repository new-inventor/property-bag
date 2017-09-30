<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 31.08.17
 */

namespace NewInventor\PropertyBag\Normalizer;


use NewInventor\PropertyBag\PropertyBagInterface;
use NewInventor\TypeChecker\TypeChecker;

class PropertyBagNormalizer extends AbstractNormalizer
{
    /**
     * @var string
     */
    protected $class;
    
    /**
     * PropertyBagNormalizer constructor.
     *
     * @param string $class
     *
     * @throws \NewInventor\TypeChecker\Exception\TypeException
     * @throws \RuntimeException
     */
    public function __construct(string $class)
    {
        if (!class_exists($class)) {
            throw new \RuntimeException("Class {$class} not found.");
        }
        $this->class = $class;
    }
    
    protected function validateInputTypes($value)
    {
        TypeChecker::check($value)->tarray()->tnull()->types(PropertyBagInterface::class)->fail();
    }
    
    protected function parseInputValue($value)
    {
        $value = parent::parseInputValue($value);
        if (is_array($value)) {
            $class = '\\' . $this->class;
            /** @var PropertyBagInterface $obj */
            $obj = new $class();
            $obj->load($value);
            $value = $obj;
        }
        
        return $value;
    }
    
    public static function asString(...$config): string
    {
        $segments = [];
        if(isset($config[0])){
            $segments[] = $config[0];
        }
        return parent::asString() . '_' . implode('_', $segments);
    }
    
    public function __toString(): string
    {
        return static::asString($this->class);
    }
}