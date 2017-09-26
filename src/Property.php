<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 29.08.17
 */

namespace NewInventor\PropertyBag;


use NewInventor\PropertyBag\Formatter\FormatterInterface;
use NewInventor\PropertyBag\Normalizer\NormalizerInterface;
use NewInventor\TypeChecker\TypeChecker;

class Property
{
    /** @var mixed */
    private $value;
    /** @var NormalizerInterface */
    private $normalizer;
    /** @var FormatterInterface */
    private $formatter;
    
    /**
     * TransportQueryParameter constructor.
     *
     * @param mixed $default
     */
    public function __construct($default = null)
    {
        $this->value = $default;
    }
    
    public function setNormalizer(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    
        if ($this->value !== null) {
            $this->value = $this->normalizer->normalize($this->value);
        }
        
        return $this;
    }
    
    public function setFormatter(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
        
        return $this;
    }
    
    public function setValue($value): void
    {
        if ($this->normalizer !== null) {
            $value = $this->normalizer->normalize($value);
        }
        $this->value = $value;
    }
    
    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
    
    public function getFormattedValue()
    {
        if ($this->formatter !== null) {
            return $this->formatter->format($this->value);
        }
        if(TypeChecker::check($this->value)->tscalar()->callback(
            function ($value) {
                return is_object($value) && method_exists($value, '__toString');
            }
        )->result()){
            return is_scalar($this->value) ? $this->value : (string)$this->value;
        }
        
        return $this->value;
    }
    
    /**
     * @param array $config
     *
     * @return static
     */
    public static function make(...$config)
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        return new static(...$config);
    }
}