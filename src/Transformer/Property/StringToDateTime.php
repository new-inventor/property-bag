<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 09.08.17
 */

namespace NewInventor\DataStructure\Transformer\Property;


use NewInventor\TypeChecker\TypeChecker;

class StringToDateTime extends Transformer
{
    /** @var string */
    private $format;
    
    /**
     * DateTimeNormalizer constructor.
     *
     * @param string $format
     */
    public function __construct(string $format = 'd.m.Y H:i:s')
    {
        $this->format = $format;
    }
    
    protected function validateInputTypes($value)
    {
        TypeChecker::check($value)->tstring()->fail();
    }
    
    protected function parseInputValue($value)
    {
        $value = \DateTime::createFromFormat($this->format, $value);
        if ($value !== false) {
            return $value;
        }
        throw new \InvalidArgumentException("Date format invalid. (must be '{$this->format}')");
    }
}