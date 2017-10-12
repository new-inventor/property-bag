<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 29.08.17
 */

namespace NewInventor\PropertyBag;


use NewInventor\DataStructure\Exception\PropertyNotFoundException;

interface PropertyBagInterface
{
    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function add(string $name, $value = null);
    
    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool;
    
    /**
     * @param string $name
     *
     * @return $this
     */
    public function remove(string $name);
    
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
     * @return array
     */
    public function toArray(): array;
    
    /**
     * @param array $properties
     *
     * @return $this
     * @throws PropertyNotFoundException
     */
    public function load(array $properties = []);
    
    /**
     * @return static
     * @throws PropertyNotFoundException
     */
    public static function make();
}