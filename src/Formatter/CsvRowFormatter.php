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
    protected $separator = ',';
    /** @var string */
    protected $enclosure = '"';
    /** @var string */
    protected $escape = '\\';
    
    /**
     * ArrayFormatter constructor.
     *
     * @param FormatterInterface[]|string[] $formatters
     *
     * @throws \NewInventor\TypeChecker\Exception\TypeException
     * @throws \InvalidArgumentException
     */
    public function __construct(...$formatters)
    {
        $i = 0;
        foreach($formatters as $key => $formatter){
            if($formatter instanceof FormatterInterface || $formatter === null){
                $this->formatters[] = $formatter;
            }else{
                $i = $key;
                break;
            }
        }
        if($i === 0 && !is_string($formatter[$i])){
            return;
        }
        if(count($formatters) > $i + 3){
            throw new \InvalidArgumentException('Separator, Enclosure, Escepe parameters should be in the end of parameters list');
        }
        if(isset($formatters[$i])){
            TypeChecker::check($formatters[$i])->tstring()->fail();
            $this->separator = $formatters[$i];
        }
        if(isset($formatters[$i + 1])){
            TypeChecker::check($formatters[$i])->tstring()->fail();
            $this->enclosure = $formatters[$i + 1];
        }
        if(isset($formatters[$i + 2])){
            TypeChecker::check($formatters[$i])->tstring()->fail();
            $this->escape = $formatters[$i + 2];
        }
    }
    
    /**
     * @return string
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }
    
    /**
     * @param string $separator
     */
    public function setSeparator(string $separator)
    {
        $this->separator = $separator;
    }
    
    /**
     * @return string
     */
    public function getEnclosure(): string
    {
        return $this->enclosure;
    }
    
    /**
     * @param string $enclosure
     */
    public function setEnclosure(string $enclosure)
    {
        $this->enclosure = $enclosure;
    }
    
    /**
     * @return string
     */
    public function getEscape(): string
    {
        return $this->escape;
    }
    
    /**
     * @param string $escape
     */
    public function setEscape(string $escape)
    {
        $this->escape = $escape;
    }
    
    protected function formatInputValue($value): ?string
    {
        if (
            !empty($this->formatters) &&
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
                if($currentFormatter !== null){
                    $value[$key] = $currentFormatter->format($item);
                }
                if (array_key_exists(++$i, $this->formatters)) {
                    $currentFormatter = $this->formatters[$i];
                }
            }
        }
        if($value instanceof \Iterator && $value instanceof \ArrayAccess){
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
        }, ['enclosure' => $this->enclosure, 'escape' => $this->escape]);
        $value = implode($this->separator, $value);
        
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
        return parent::asString() . '_' . implode('_', $normalizers);
    }
    
    public function __toString(): string
    {
        return static::asString(...$this->formatters);
    }
}