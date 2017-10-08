<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 12.09.17
 */

namespace NewInventor\DataStructure\Transformer;


class StringToPhone extends Transformer
{
    protected function transformInputValue($value)
    {
        $value = preg_replace('/\D/', '', $value);
        
        return '+' . $value;
    }
}