<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 29.08.17
 */

namespace NewInventor\PropertyBag\Formatter;


use NewInventor\TypeChecker\TypeChecker;

class DateTimeFormatter extends AbstractFormatter
{
    /**
     * @var string
     */
    private $format;
    
    /**
     * DateTimeFormatter constructor.
     *
     * @param string $format
     */
    public function __construct(string $format = 'd.m.Y H:i:s')
    {
        $this->format = $format;
    }
    
    /**
     * @param \DateTime $value
     *
     * @return string
     */
    public function formatInputValue($value): ?string
    {
        return $value->format($this->format);
    }
    
    protected function validateInputTypes($value)
    {
        TypeChecker::check($value)->tnull()->types(\DateTime::class)->fail();
    }
    
    public static function asString(...$config): string
    {
        $res = parent::asString();
        if(isset($config[0])){
            $res .= '_' . $config[0];
        }
        return $res;
    }
    
    public function __toString(): string
    {
        return static::asString($this->format);
    }
}