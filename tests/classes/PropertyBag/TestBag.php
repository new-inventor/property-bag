<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 06.09.17
 */

namespace TestsPropertyBag;


use NewInventor\PropertyBag\Formatter\DateTimeFormatter;
use NewInventor\PropertyBag\Normalizer\DateTimeNormalizer;
use NewInventor\PropertyBag\Normalizer\StringNormalizer;
use NewInventor\PropertyBag\Property;
use NewInventor\PropertyBag\PropertyBag;

class TestBag extends PropertyBag
{
    protected function getProperties(): array
    {
        return [
            'prop1' => Property::make(),
            'prop2' => Property::make(1),
            'prop3' => Property::make()->setNormalizer(StringNormalizer::make()),
            'prop4' => Property::make()
                ->setNormalizer(DateTimeNormalizer::make('d.m.Y'))
                ->setFormatter(DateTimeFormatter::make('d.m.Y')),
        ];
    }
}