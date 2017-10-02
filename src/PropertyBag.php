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

class PropertyBag implements PropertyBagInterface
{
    /** @var mixed[] */
    protected $properties = [];
    /** @var NormalizerInterface[] */
    protected $normalizers = [];
    /** @var FormatterInterface[] */
    protected $formatters = [];
    
    /**
     * PropertyBag constructor.
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function __construct()
    {
        $this->initProperties();
        $this->initNormalizers();
        $this->initFormatters();
    }
    
    public function getAvailableProperties(): array
    {
        return array_keys($this->properties);
    }
    
    protected function initProperties(): void
    {
    
    }
    
    protected function initNormalizers(): void
    {
    
    }
    
    protected function initFormatters(): void
    {
    
    }
    
    public function addProperty(
        string $name,
        NormalizerInterface $normalizer = null,
        FormatterInterface $formatter = null
    ) {
        $this->properties[$name] = null;
        $this->normalizers[$name] = $normalizer;
        $this->formatters[$name] = $formatter;
        
        return $this;
    }
    
    public function removeProperty(string $name)
    {
        unset($this->properties[$name], $this->normalizers[$name], $this->formatters[$name]);
        
        return $this;
    }
    
    public function hasProperty(string $name): bool
    {
        return array_key_exists($name, $this->properties);
    }
    
    public function propertyHasFormatter($name): bool
    {
        return array_key_exists($name, $this->formatters) && $this->formatters[$name] !== null;
    }
    
    public function propertyGetFormatter(string $name): ?FormatterInterface
    {
        $this->failIfNotExist($name);
        if ($this->propertyHasFormatter($name)) {
            return $this->formatters[$name];
        }
        
        return null;
    }
    
    public function propertySetFormatter(string $name, FormatterInterface $formatter)
    {
        $this->failIfNotExist($name);
        $this->formatters[$name] = $formatter;
        
        return $this;
    }
    
    public function propertyRemoveFormatter(string $name)
    {
        $this->failIfNotExist($name);
        unset($this->formatters[$name]);
        
        return $this;
    }
    
    public function propertyHasNormalizer($name): bool
    {
        return array_key_exists($name, $this->normalizers) && $this->normalizers[$name] !== null;
    }
    
    public function propertyGetNormalizer(string $name): ?NormalizerInterface
    {
        $this->failIfNotExist($name);
        if ($this->propertyHasNormalizer($name)) {
            return $this->normalizers[$name];
        }
        
        return null;
    }
    
    public function propertySetNormalizer(string $name, NormalizerInterface $normalizer)
    {
        $this->failIfNotExist($name);
        $this->normalizers[$name] = $normalizer;
        
        return $this;
    }
    
    public function propertyRemoveNormalizer(string $name)
    {
        $this->failIfNotExist($name);
        unset($this->normalizers[$name]);
        
        return $this;
    }
    
    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     * @throws PropertyNotFoundException
     */
    public function set(string $name, $value)
    {
        $this->failIfNotExist($name);
        if ($this->propertyHasNormalizer($name)) {
            $value = $this->normalizers[$name]->normalize($value);
        }
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
     * @param string $name
     *
     * @return mixed
     * @throws PropertyNotFoundException
     */
    public function getFormatted(string $name)
    {
        $this->failIfNotExist($name);
        
        return $this->propertyHasFormatter($name) ?
            $this->formatters[$name]->format($this->properties[$name]) :
            $this->properties[$name];
    }
    
    /**
     * Returns multidimensional array of strings
     * @return array
     * @throws PropertyNotFoundException
     */
    public function toFormattedArray(): array
    {
        $res = $this->toRawArray();
        foreach ($res as $param => $value) {
            if ($this->propertyHasFormatter($param)) {
                $res[$param] = $this->formatters[$param]->format($value);
            }
        }
        
        return $res;
    }
    
    public function toRawArray(): array
    {
        return $this->properties;
    }
    
    /**
     * @param $name
     *
     * @throws PropertyNotFoundException
     */
    public function failIfNotExist($name): void
    {
        if (!$this->hasProperty($name)) {
            throw new PropertyNotFoundException($name, get_class($this));
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
    
    /**
     * @param array $properties
     *
     * @return $this
     * @throws PropertyNotFoundException
     */
    public function load(array $properties = [])
    {
        foreach ($properties as $name => $value) {
            $this->set($name, $value);
        }
        
        return $this;
    }
}