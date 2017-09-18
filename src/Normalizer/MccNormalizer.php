<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 14.09.17
 */

namespace NewInventor\PropertyBag\Normalizer;


use NewInventor\PropertyBag\MobileCountryCodes;

class MccNormalizer extends EnumNormalizer
{
    public function __construct()
    {
        parent::__construct(MobileCountryCodes::$list, IntNormalizer::make());
    }
}