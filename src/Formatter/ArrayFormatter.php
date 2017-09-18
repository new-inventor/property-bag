<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 06.09.17
 */

namespace NewInventor\PropertyBag\Formatter;


use NewInventor\TypeChecker\TypeChecker;

class ArrayFormatter extends AbstractFormatter
{
    /** @var FormatterInterface[] */
    protected $formatters;
    /** @var string */
    private $separator = ',';
    
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
    public function getSeparator(): string
    {
        return $this->separator;
    }
    
    /**
     * @param string $separator
     *
     * @return $this
     */
    public function setSeparator(string $separator)
    {
        $this->separator = $separator;
        
        return $this;
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
                if ($currentFormatter !== null) {
                    $value[$key] = $currentFormatter->format($item);
                }
                if (array_key_exists(++$i, $this->formatters)) {
                    $currentFormatter = $this->formatters[$i];
                }
            }
        }
        if ($value instanceof \Iterator) {
            $value = implode($this->separator, iterator_to_array($value));
        } else {
            $value = implode($this->separator, $value);
        }
        
        return parent::formatInputValue($value);
    }
    
    protected function validateInputTypes($value)
    {
        TypeChecker::check($value)->tnull()->types(\Iterator::class, \ArrayAccess::class)
            ->inner()->tnull()->tscalar()->callback(
                function ($value) {
                    return is_object($value) && method_exists($value, '__toString');
                }
            )->fail();
    }
}