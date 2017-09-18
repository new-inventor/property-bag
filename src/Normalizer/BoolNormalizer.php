<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 10.08.17
 */

namespace NewInventor\PropertyBag\Normalizer;


class BoolNormalizer extends AbstractNormalizer
{
    /** @var array */
    private $true;
    /** @var array */
    private $false;
    
    /**
     * BoolNormalizer constructor.
     *
     * @param array $true
     * @param array $false
     */
    public function __construct(array $true = [], array $false = [])
    {
        $this->true = $true;
        $this->false = $false;
    }
    
    protected function parseInputValue($value)
    {
        $value = parent::parseInputValue($value);
        if ($this->true !== [] && in_array($value, $this->true, true)) {
            return true;
        }
        if ($this->false !== [] && in_array($value, $this->false, true)) {
            return false;
        }
    
        return $value;
    }
    
    protected function normalizeInputValue($value)
    {
        return (boolean)$value;
    }
}