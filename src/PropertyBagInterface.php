<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 29.08.17
 */

namespace NewInventor\PropertyBag;


use NewInventor\PropertyBag\Exception\PropertyNotFoundException;
use NewInventor\PropertyBag\Formatter\FormatterInterface;
use NewInventor\PropertyBag\Normalizer\NormalizerInterface;

interface PropertyBagInterface
{
    public function addProperty(
        string $name,
        NormalizerInterface $normalizer = null,
        FormatterInterface $formatter = null
    );
    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     * @throws PropertyNotFoundException
     */
    public function set(string $name, $value);
    
    /**
     * @param string $name
     *
     * @return mixed
     * @throws PropertyNotFoundException
     */
    public function get(string $name);
    
    /**
     * @param string $name
     *
     * @return mixed
     * @throws PropertyNotFoundException
     */
    public function getFormatted(string $name);
    
    /**
     * Returns multidimensional array of strings
     * @return array
     */
    public function toFormattedArray(): array;
    
    public function toRawArray(): array;
    
    /**
     * @param $name
     *
     * @throws PropertyNotFoundException
     */
    public function failIfNotExist($name): void;
    
    /**
     * @return static
     * @throws PropertyNotFoundException
     */
    public static function make();
    
    public function load(array $properties = []);
}