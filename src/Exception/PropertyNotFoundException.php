<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 29.08.17
 */

namespace NewInventor\PropertyBag\Exception;


class PropertyNotFoundException extends \InvalidArgumentException
{
    /**
     * ParameterNotFoundException constructor.
     *
     * @param string $name
     * @param string $class
     */
    public function __construct(string $name, string $class)
    {
        parent::__construct("Parameter '{$name}' not found in class '{$class}'.");
    }
}