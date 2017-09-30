<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 09.08.17
 */

namespace NewInventor\PropertyBag\Normalizer;


use NewInventor\TypeChecker\TypeChecker;

class CsvRowNormalizer extends ArrayNormalizer
{
    /** @var string */
    protected static $separator = ',';
    /** @var string */
    protected static $enclosure = '"';
    /** @var string */
    protected static $escape = '\\';
    
    /**
     * @return string
     */
    public static function getSeparator(): string
    {
        return self::$separator;
    }
    
    /**
     * @param string $separator
     */
    public static function setSeparator(string $separator)
    {
        self::$separator = $separator;
    }
    
    /**
     * @return string
     */
    public static function getEnclosure(): string
    {
        return self::$enclosure;
    }
    
    /**
     * @param string $enclosure
     */
    public static function setEnclosure(string $enclosure)
    {
        self::$enclosure = $enclosure;
    }
    
    /**
     * @return string
     */
    public static function getEscape(): string
    {
        return self::$escape;
    }
    
    /**
     * @param string $escape
     */
    public static function setEscape(string $escape)
    {
        self::$escape = $escape;
    }
    
    protected function validateInputTypes($value)
    {
        TypeChecker::check($value)->tnull()->tscalar()->callback(
            function ($value) {
                return is_object($value) && method_exists($value, '__toString');
            }
        )->inner()->tnull()->tscalar()->callback(
            function ($value) {
                return is_object($value) && method_exists($value, '__toString');
            }
        )->fail();
    }
    
    protected function parseInputValue($value)
    {
        if (is_string($value)) {
            $value = str_getcsv($value, self::$separator, self::$enclosure, self::$escape);
        }
        if (!is_array($value)) {
            $value = (array)$value;
        }
    
        return $value;
    }
    
    public static function asString(...$config): string
    {
        return parent::asString(...$config) . '_' . self::$separator . '_' . self::$enclosure . '_' . self::$escape;
    }
}