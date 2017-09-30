<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 06.09.17
 */

namespace NewInventor\PropertyBag\Formatter;


use NewInventor\TypeChecker\TypeChecker;

class CsvRowFormatter extends AbstractFormatter
{
    /** @var FormatterInterface[] */
    protected $formatters;
    /** @var string */
    protected static $separator = ',';
    /** @var string */
    protected static $enclosure = '"';
    /** @var string */
    protected static $escape = '\\';
    
    /**
     * ArrayFormatter constructor.
     *
     * @param FormatterInterface[] $formatters
     */
    public function __construct(...$formatters)
    {
        $this->formatters = $formatters;
    }
    
    /**
     * @return string
     */
    public static function getSeparator(): string
    {
        return self::$separator;
    }
    
    /**
     * @param string $separator
     */
    public static function setSeparator(string $separator)
    {
        self::$separator = $separator;
    }
    
    /**
     * @return string
     */
    public static function getEnclosure(): string
    {
        return self::$enclosure;
    }
    
    /**
     * @param string $enclosure
     */
    public static function setEnclosure(string $enclosure)
    {
        self::$enclosure = $enclosure;
    }
    
    /**
     * @return string
     */
    public static function getEscape(): string
    {
        return self::$escape;
    }
    
    /**
     * @param string $escape
     */
    public static function setEscape(string $escape)
    {
        self::$escape = $escape;
    }
    
    protected function formatInputValue($value): ?string
    {
        if (
            $this->formatters !== [] &&
            (
                is_array($value) ||
                (
                    $value instanceof \Iterator && $value instanceof \ArrayAccess
                )
            )
        ) {
            $i = 0;
            $currentFormatter = $this->formatters[$i];
            foreach ($value as $key => $item) {
                if(!is_scalar($value[$key]) || (is_object($value[$key]) && !method_exists($value[$key], '__toString'))){
                    throw new \InvalidArgumentException('Inner value of iterator must be scalar or object with __toString() method.');
                }
                if ($currentFormatter !== null) {
                    $value[$key] = $currentFormatter->format($item);
                }
                if (array_key_exists(++$i, $this->formatters)) {
                    $currentFormatter = $this->formatters[$i];
                }
            }
            $value = iterator_to_array($value);
        }
        array_walk($value, function (&$item, $key, $params){
            if(is_string($item)){
                $item = $params['enclosure'] .
                        str_replace(
                            $params['enclosure'],
                            $params['escape'] . $params['enclosure'],
                            $item
                        ) .
                        $params['enclosure'];
            }
        }, ['enclosure' => self::$enclosure, 'escape' => self::$escape]);
        $value = implode(self::$separator, $value);
        
        return $value;
    }
    
    protected function validateInputTypes($value)
    {
        TypeChecker::check($value)
            ->tnull()
            ->callback(
                function ($value) {
                    return $value instanceof \Iterator && $value instanceof \ArrayAccess;
                }
            )
            ->tarray()
            ->inner()
            ->tnull()
            ->tscalar()
            ->callback(
                function ($value) {
                    return is_object($value) && method_exists($value, '__toString');
                }
            )
            ->fail();
    }
    
    public static function asString(...$config): string
    {
        $normalizers = [];
        foreach($config as $normalizer){
            $normalizers[] = (string)$normalizer;
        }
        return parent::asString() . '_' . implode('_', $normalizers) . '_' . self::$separator . '_' . self::$enclosure . '_' . self::$escape;
    }
    
    public function __toString(): string
    {
        return static::asString(...$this->formatters);
    }
}