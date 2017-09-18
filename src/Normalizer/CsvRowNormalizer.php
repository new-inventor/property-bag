<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 09.08.17
 */

namespace NewInventor\PropertyBag\Normalizer;


use NewInventor\TypeChecker\TypeChecker;

class CsvRowNormalizer extends ArrayNormalizer
{
    /** @var string */
    protected $separator = ',';
    
    /**
     * @return string
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }
    
    /**
     * @param string $separator
     *
     * @return $this
     */
    public function setSeparator(string $separator)
    {
        $this->separator = $separator;
    
        return $this;
    }
    
    protected function validateInputTypes($value)
    {
        TypeChecker::check($value)->tnull()->tscalar()->callback(
            function ($value) {
                return is_object($value) && method_exists($value, '__toString');
            }
        )->inner()->tnull()->tscalar()->callback(
            function ($value) {
                return is_object($value) && method_exists($value, '__toString');
            }
        )->fail();
    }
    
    protected function parseInputValue($value)
    {
        if (is_string($value)) {
            $value = explode($this->separator, (string)$value);
        }
        if (!is_array($value)) {
            $value = (array)$value;
        }
    
        return $value;
    }
}