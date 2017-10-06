<?php
/**
 * Project: property-bag
 * User: george
 * Date: 05.10.17
 */

namespace NewInventor\DataStructure;


use NewInventor\DataStructure\Transformer\Property\TransformerInterface;

interface Loadable
{
    // fail if property does not exists
    const STRATEGY_STRICT = 0;
    // add property if does not exists
    const STRATEGY_ADD = 1;
    // do nothing if does not exists
    const STRATEGY_SKIP = 2;
    
    /**
     * Load object properties from array
     *
     * @param array                $properties
     * @param TransformerInterface $forwardTransformer
     * @param int                  $strategy
     *
     * @return $this
     */
    public function load(
        array $properties = [],
        TransformerInterface $forwardTransformer,
        int $strategy = self::STRATEGY_STRICT
    );
}