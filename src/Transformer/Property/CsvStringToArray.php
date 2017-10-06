<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 09.08.17
 */

namespace NewInventor\DataStructure\Transformer\Property;


use NewInventor\TypeChecker\TypeChecker;

class CsvStringToArray extends ToArray
{
    /** @var string */
    protected $separator;
    /** @var string */
    protected $enclosure;
    /** @var string */
    protected $escape;
    
    /**
     * ArrayFormatter constructor.
     *
     * @param string $separator
     * @param string $enclosure
     * @param string $escape
     */
    public function __construct(string $separator = ',', string $enclosure = '"', string $escape = '\\')
    {
        $this->separator = $separator;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
    }
    
    /**
     * @param $value
     *
     * @throws \NewInventor\TypeChecker\Exception\TypeException
     */
    protected function validateInputTypes($value)
    {
        TypeChecker::check($value)->tstring()->fail();
    }
    
    protected function transformInputValue($value)
    {
        return str_getcsv($value, $this->separator, $this->enclosure, $this->escape);
    }
}