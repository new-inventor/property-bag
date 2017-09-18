<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 09.08.17
 */

namespace NewInventor\PropertyBag\Normalizer;


use NewInventor\PropertyBag\Exception\NormalizeException;
use NewInventor\TypeChecker\TypeChecker;

class DateTimeNormalizer extends AbstractNormalizer
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
        TypeChecker::check($value)->tnull()->tstring()->types(\DateTime::class)->fail();
    }
    
    protected function parseInputValue($value)
    {
        $value = parent::parseInputValue($value);
        if (is_string($value)) {
            $value = \DateTime::createFromFormat($this->format, $value);
            if ($value !== false) {
                return $value;
            }
            throw new NormalizeException(__CLASS__, $value, "Date format invalid. (must be '{$this->format}')");
        }
        
        return $value;
    }
}