<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 29.08.17
 */

namespace NewInventor\PropertyBag;


use Psr\SimpleCache\CacheInterface;
use NewInventor\PropertyBag\Exception\PropertyNotFoundException;
use NewInventor\PropertyBag\Formatter\ArrayFormatter;
use NewInventor\PropertyBag\Formatter\BoolFormatter;
use NewInventor\PropertyBag\Formatter\DateTimeFormatter;
use NewInventor\PropertyBag\Formatter\FormatterInterface;
use NewInventor\PropertyBag\Normalizer\ArrayNormalizer;
use NewInventor\PropertyBag\Normalizer\BoolNormalizer;
use NewInventor\PropertyBag\Normalizer\CsvRowNormalizer;
use NewInventor\PropertyBag\Normalizer\DateTimeNormalizer;
use NewInventor\PropertyBag\Normalizer\EmptyNormalizer;
use NewInventor\PropertyBag\Normalizer\EnumNormalizer;
use NewInventor\PropertyBag\Normalizer\FloatNormalizer;
use NewInventor\PropertyBag\Normalizer\FloatRangeNormalizer;
use NewInventor\PropertyBag\Normalizer\IntNormalizer;
use NewInventor\PropertyBag\Normalizer\IntRangeNormalizer;
use NewInventor\PropertyBag\Normalizer\NormalizerInterface;
use NewInventor\PropertyBag\Normalizer\PropertyBagNormalizer;
use NewInventor\PropertyBag\Normalizer\RangeNormalizer;
use NewInventor\PropertyBag\Normalizer\StringNormalizer;

class PropertyBag implements PropertyBagInterface
{
    /** @var Property[] */
    protected $properties = [];
    /** @var CacheInterface */
    protected static $cacheDriver;
    
    /**
     * PropertyBag constructor.
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function __construct()
    {
        if (self::$cacheDriver !== null) {
            $cacheKey = $this->getCacheKey();
            if (self::$cacheDriver->has($cacheKey)) {
                $this->preloadClasses();
                $this->properties = self::$cacheDriver->get($cacheKey);
            } else {
                $this->properties = $this->getProperties();
                self::$cacheDriver->set($cacheKey, $this->properties);
            }
        } else {
            $this->properties = $this->getProperties();
        }
    }
    
    public function cacheProperties()
    {
        $cacheKey = $this->getCacheKey();
        if (self::$cacheDriver->has($cacheKey)) {
            self::$cacheDriver->delete($cacheKey);
        }
        self::$cacheDriver->set($cacheKey, $this->properties);
    }
    
    protected function getCacheKey()
    {
        return str_replace(['\\', '/'], '_', get_class($this));
    }
    
    public static function setCacheDriver(CacheInterface $driver = null)
    {
        self::$cacheDriver = $driver;
    }
    
    public static function getCacheDriver(): ?CacheInterface
    {
        return self::$cacheDriver;
    }
    
    protected function preloadClasses()
    {
        ArrayNormalizer::class;
        BoolNormalizer::class;
        CsvRowNormalizer::class;
        DateTimeNormalizer::class;
        EmptyNormalizer::class;
        EnumNormalizer::class;
        FloatNormalizer::class;
        FloatRangeNormalizer::class;
        IntNormalizer::class;
        IntRangeNormalizer::class;
        NormalizerInterface::class;
        PropertyBagNormalizer::class;
        RangeNormalizer::class;
        StringNormalizer::class;
        Property::class;
        __CLASS__;
        PropertyBagInterface::class;
        BoolFormatter::class;
        ArrayFormatter::class;
        DateTimeFormatter::class;
        FormatterInterface::class;
    }
    
    protected function getProperties(): array
    {
        return [];
    }
    
    public function addProperty(string $name, Property $property)
    {
        $this->properties[$name] = $property;
        $this->cacheProperties();
    
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
        $this->properties[$name]->setValue($value);
        
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
        
        return $this->properties[$name]->getValue();
    }
    
    /**
     * @param string $name
     *
     * @return string|null
     * @throws PropertyNotFoundException
     */
    public function getFormatted(string $name): ?string
    {
        $this->failIfNotExist($name);
        
        return $this->properties[$name]->getFormattedValue();
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
            $res[$param] = $this->getFormatted($param);
        }
        
        return $res;
    }
    
    public function toRawArray(): array
    {
        $res = [];
        foreach ($this->properties as $parameterName => $parameter) {
            $value = $parameter->getValue();
            if ($value === null) {
                continue;
            }
            $res[$parameterName] = $value;
        }
        
        return $res;
    }
    
    /**
     * @param $name
     *
     * @throws PropertyNotFoundException
     */
    public function failIfNotExist($name): void
    {
        if (!array_key_exists($name, $this->properties)) {
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