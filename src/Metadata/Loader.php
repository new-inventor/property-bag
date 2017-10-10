<?php
/**
 * Project: property-bag
 * User: george
 * Date: 10.10.17
 */

namespace NewInventor\PropertyBag\Metadata;


use NewInventor\DataStructure\Metadata\Loader as BaseLoader;

class Loader extends BaseLoader
{
    protected function constructMetadata($path)
    {
        return (new Metadata())->loadConfig($path);
    }
}