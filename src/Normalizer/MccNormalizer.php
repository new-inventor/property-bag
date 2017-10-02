<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 14.09.17
 */

namespace NewInventor\PropertyBag\Normalizer;


use NewInventor\PropertyBag\Accessory\MobileCountryCodes;

class MccNormalizer extends EnumNormalizer
{
    public function __construct()
    {
        parent::__construct(MobileCountryCodes::$list, IntNormalizer::make());
    }
    
    public static function asString(...$config): string
    {
        return static::class;
    }
}