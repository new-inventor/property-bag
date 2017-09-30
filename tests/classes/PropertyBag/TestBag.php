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
    protected function initProperties(): void
    {
        $this->properties = [
            'prop1' => null,
            'prop2' => 1,
            'prop3' => null,
            'prop4' => null,
        ];
    }
    
    protected function initNormalizers(): void
    {
        $this->normalizers = [
            'prop3' => StringNormalizer::make(),
            'prop4' => DateTimeNormalizer::make('d.m.Y'),
        ];
    }
    
    protected function initFormatters(): void
    {
        $this->formatters = [
            'prop4' => DateTimeFormatter::make('d.m.Y')
        ];
    }
}