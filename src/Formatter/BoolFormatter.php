<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 29.08.17
 */

namespace NewInventor\PropertyBag\Formatter;


use NewInventor\TypeChecker\TypeChecker;

class BoolFormatter extends AbstractFormatter
{
    /** @var string */
    protected $true;
    /** @var string */
    protected $false;
    
    /**
     * BoolFormatter constructor.
     *
     * @param string $true
     * @param string $false
     */
    public function __construct(string $true = '1', string $false = '0')
    {
        $this->true = $true;
        $this->false = $false;
    }
    
    public function formatInputValue($value): ?string
    {
        return $value ? $this->true : $this->false;
    }
    
    public function validateInputTypes($value)
    {
        TypeChecker::check($value)->tnull()->tbool()->fail();
    }
}