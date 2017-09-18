<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 11.09.17
 */

namespace NewInventor\PropertyBag\Normalizer;


abstract class AbstractNormalizer implements NormalizerInterface
{
    public static function make(...$config): NormalizerInterface
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        return new static(...$config);
    }
    
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function normalize($value)
    {
        if ($value === null) {
            return null;
        }
        $this->validateInputTypes($value);
        $value = $this->parseInputValue($value);
        
        return $this->normalizeInputValue($value);
    }
    
    /**
     * @param $value
     *
     * @throw \Trowable
     */
    protected function validateInputTypes($value)
    {
    
    }
    
    /**
     * @param $value
     *
     * @return mixed
     */
    protected function parseInputValue($value)
    {
        if ($value === null) {
            return null;
        }
        
        return $value;
    }
    
    /**
     * @param $value
     *
     * @return mixed
     */
    protected function normalizeInputValue($value)
    {
        return $value;
    }
}