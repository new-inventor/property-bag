<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 29.08.17
 */

namespace NewInventor\PropertyBag\Formatter;


interface FormatterInterface
{
    public function format($value);
    
    public static function make(...$config): FormatterInterface;
    
    public function __toString(): string;
}