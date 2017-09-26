<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 11.09.17
 */

namespace NewInventor\PropertyBag\Formatter;


abstract class AbstractFormatter implements FormatterInterface
{
    public static function make(...$config): FormatterInterface
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        return new static(...$config);
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
    
    protected function formatInputValue($value)
    {
        return (string)$value;
    }
}