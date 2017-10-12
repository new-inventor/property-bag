<?php
/**
 * Project: property-bag
 * User: george
 * Date: 04.10.17
 */

namespace NewInventor\PropertyBag\Metadata;


use NewInventor\DataStructure\Metadata\Metadata as BaseMetadata;
use NewInventor\PropertyBag\PropertyBag;

class Metadata extends BaseMetadata
{
    /** @var string */
    public $parent = PropertyBag::class;
    /** @var bool */
    public $abstract = false;
    /** @var string[] */
    public $getters = [];
    /** @var string[] */
    public $setters = [];
}