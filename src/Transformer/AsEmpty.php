<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 03.09.17
 */

namespace NewInventor\DataStructure\Transformer;


class AsEmpty extends Transformer
{
    protected function transformInputValue($value)
    {
        if (empty($value)) {
            return null;
        }
    
        return $value;
    }
}