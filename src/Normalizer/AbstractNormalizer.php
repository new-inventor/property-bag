<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 11.09.17
 */

namespace NewInventor\PropertyBag\Normalizer;


abstract class AbstractNormalizer implements NormalizerInterface
{
    private static $pool = [];
    
    public static function make(...$config): NormalizerInterface
    {
        $key = static::asString(...$config);
        if(!isset(self::$pool[$key])){
            /** @noinspection PhpIncompatibleReturnTypeInspection */
            /** @noinspection PhpMethodParametersCountMismatchInspection */
            self::$pool[$key] = new static(...$config);
        }
        return self::$pool[$key];
    }
    
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function normalize($value)
    {
        if ($value === null) {
            return null;
        }
        $this->validateInputTypes($value);
        $value = $this->parseInputValue($value);
        
        return $this->normalizeInputValue($value);
    }
    
    /**
     * @param $value
     *
     * @throw \Trowable
     */
    protected function validateInputTypes($value)
    {
    
    }
    
    /**
     * @param $value
     *
     * @return mixed
     */
    protected function parseInputValue($value)
    {
        if ($value === null) {
            return null;
        }
        
        return $value;
    }
    
    /**
     * @param $value
     *
     * @return mixed
     */
    protected function normalizeInputValue($value)
    {
        return $value;
    }
    
    public static function asString(...$config): string
    {
        return static::class;
    }
    
    public function __toString(): string
    {
        return static::asString();
    }
}