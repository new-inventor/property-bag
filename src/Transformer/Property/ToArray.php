<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 10.08.17
 */

namespace NewInventor\DataStructure\Transformer\Property;


use NewInventor\DataStructure\Arrayable;

class ToArray extends Transformer
{
    protected function transformInputValue($value)
    {
        if (is_object($value) && in_array(Arrayable::class, class_implements($value), true)) {
            /** @noinspection PhpUndefinedMethodInspection */
            $value = $value->toArray();
        }
        if ($value instanceof \Iterator && $value instanceof \ArrayAccess) {
            $value = iterator_to_array($value);
        }
        if (!is_array($value)) {
            $value = (array)$value;
        }
        
        return $value;
    }
}