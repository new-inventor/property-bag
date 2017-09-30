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
     *
     * @throws \NewInventor\TypeChecker\Exception\TypeException
     */
    public function __construct($true = '1', $false = '0')
    {
        TypeChecker::check($true)->tstring()->tint()->fail();
        TypeChecker::check($false)->tstring()->tint()->fail();
        $this->true = $true;
        $this->false = $false;
    }
    
    public function formatInputValue($value)
    {
        return $value ? $this->true : $this->false;
    }
    
    public function validateInputTypes($value)
    {
        TypeChecker::check($value)->tnull()->tbool()->fail();
    }
    
    public static function asString(...$config): string
    {
        $values = [];
        if (isset($config[0])) {
            $values[] = (string)$config[0];
        }
        if (isset($config[1])) {
            $values[] = (string)$config[1];
        }
        
        return parent::asString() . '_' . implode('_', $values);
    }
    
    public function __toString(): string
    {
        return static::asString($this->true, $this->false);
    }
}