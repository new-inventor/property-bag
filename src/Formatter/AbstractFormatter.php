<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 11.09.17
 */

namespace NewInventor\PropertyBag\Formatter;


abstract class AbstractFormatter implements FormatterInterface
{
    private static $pool = [];
    
    public static function make(...$config): FormatterInterface
    {
        $key = static::asString(...$config);
        if(!isset(self::$pool[$key])){
            /** @noinspection PhpIncompatibleReturnTypeInspection */
            /** @noinspection PhpMethodParametersCountMismatchInspection */
            self::$pool[$key] = new static(...$config);
        }
        return self::$pool[$key];
    }
    
    public function format($value)
    {
        if ($value === null) {
            return null;
        }
        $this->validateInputTypes($value);
        
        return $this->formatInputValue($value);
    }
    
    /**
     * @param $value
     *
     * @throw \Trowable
     */
    protected function validateInputTypes($value)
    {
    
    }
    
    abstract protected function formatInputValue($value);
    
    public static function asString(...$config): string
    {
        return static::class;
    }
    
    public function __toString(): string
    {
        return static::asString();
    }
}