<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 09.08.17
 */

namespace NewInventor\PropertyBag\Normalizer;


Interface NormalizerInterface
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function normalize($value);
    
    public static function make(...$config): NormalizerInterface;
    
    public static function asString(): string;
    public function __toString(): string;
}