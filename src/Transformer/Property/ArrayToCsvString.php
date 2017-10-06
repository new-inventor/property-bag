<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 06.09.17
 */

namespace NewInventor\DataStructure\Transformer\Property;


use NewInventor\TypeChecker\TypeChecker;

class ArrayToCsvString extends Transformer
{
    /** @var string */
    protected $separator;
    /** @var string */
    protected $enclosure;
    /** @var string */
    protected $escape;
    /** @var bool */
    protected $encloseAll;
    
    /**
     * ArrayFormatter constructor.
     *
     * @param string $separator
     * @param string $enclosure
     * @param string $escape
     * @param bool   $encloseAll
     */
    public function __construct(
        string $separator = ',',
        string $enclosure = '"',
        string $escape = '\\',
        bool $encloseAll = false
    ) {
        $this->separator = $separator;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
        $this->encloseAll = $encloseAll;
    }
    
    protected function transformInputValue($value): ?string
    {
        return implode($this->separator, array_map([$this, 'itemToCsvFormat'], $value));
    }
    
    protected function itemToCsvFormat($item)
    {
        if (
            is_string($item) &&
            (
                $this->encloseAll ||
                (
                    strpos($item, $this->enclosure) !== false ||
                    strpos($item, $this->escape) !== false ||
                    strpos($item, $this->separator) !== false
                )
            )
        ) {
            $item = $this->enclosure .
                    str_replace($this->enclosure, $this->enclosure . $this->enclosure, $item) .
                    $this->enclosure;
        }
        if (is_bool($item)) {
            $item = $item ? 1 : 0;
        }
        
        return (string)$item;
    }
    
    protected function validateInputTypes($value)
    {
        TypeChecker::check($value)
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
}