<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 09.08.17
 */

namespace NewInventor\DataStructure\Transformer;


Interface TransformerInterface
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function transform($value);
    
    public static function make(...$config): TransformerInterface;
}