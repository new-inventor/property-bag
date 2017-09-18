<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 29.08.17
 */

namespace NewInventor\PropertyBag;


use NewInventor\PropertyBag\Formatter\FormatterInterface;
use NewInventor\PropertyBag\Normalizer\NormalizerInterface;

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
    
    public function getFormattedValue(): ?string
    {
        if ($this->formatter !== null) {
            return $this->formatter->format($this->value);
        }
        
        return (string)$this->value;
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