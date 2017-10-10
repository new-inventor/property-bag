<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 29.08.17
 */

namespace NewInventor\PropertyBag;


use NewInventor\DataStructure\Arrayable;
use NewInventor\DataStructure\DataStructureInterface;
use NewInventor\DataStructure\Exception\PropertyException;
use NewInventor\DataStructure\Exception\PropertyNotFoundException;
use NewInventor\DataStructure\Loadable;

class PropertyBag implements PropertyBagInterface, Loadable, Arrayable, DataStructureInterface
{
    /** @var mixed[] */
    protected $properties = [];
    
    public function __construct()
    {
        $this->initProperties();
    }
    
    protected function initProperties()
    {
    
    }
    
    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function add(string $name, $value = null)
    {
        $this->properties[$name] = $value;
        
        return $this;
    }
    
    /**
     * @param string $name
     *
     * @return $this
     */
    public function remove(string $name)
    {
        unset($this->properties[$name]);
        
        return $this;
    }
    
    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->properties);
    }
    
    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     * @throws PropertyException
     * @throws PropertyNotFoundException
     */
    public function set(string $name, $value)
    {
        $this->failIfNotExist($name);
        $this->properties[$name] = $value;
        
        return $this;
    }
    
    /**
     * @param string $name
     *
     * @return mixed
     * @throws PropertyNotFoundException
     */
    public function get(string $name)
    {
        $this->failIfNotExist($name);
        
        return $this->properties[$name];
    }
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->properties;
    }
    
    /**
     * @param $name
     *
     * @throws PropertyNotFoundException
     */
    protected function failIfNotExist($name): void
    {
        if (!$this->has($name)) {
            throw new PropertyNotFoundException($name);
        }
    }
    
    /**
     * @param array $config
     *
     * @return static
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     * @throws PropertyNotFoundException
     */
    public static function make(...$config)
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        return new static(...$config);
    }
    
    public function load(array $properties = [], int $strategy = self::STRATEGY_STRICT)
    {
        foreach ($properties as $name => $value) {
            try {
                $this->set($name, $value);
            } catch (PropertyNotFoundException $e) {
                if ($strategy === self::STRATEGY_STRICT) {
                    throw $e;
                }
                if ($strategy === self::STRATEGY_ADD) {
                    $this->add($name, $value);
                }
            }
        }
        
        return $this;
    }
}