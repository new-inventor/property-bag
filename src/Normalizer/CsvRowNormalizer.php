<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 09.08.17
 */

namespace NewInventor\PropertyBag\Normalizer;


use NewInventor\TypeChecker\TypeChecker;

class CsvRowNormalizer extends ArrayNormalizer
{
    /** @var string */
    protected $separator = ',';
    /** @var string */
    protected $enclosure = '"';
    /** @var string */
    protected $escape = '\\';
    
    public function __construct(...$normalizers)
    {
        $i = 0;
        foreach($normalizers as $key => $normalizer){
            if($normalizer instanceof NormalizerInterface || $normalizer === null){
                $this->normalizers[] = $normalizer;
            }else{
                $i = $key;
                break;
            }
        }
        if(count($normalizers) > $i + 3){
            throw new \InvalidArgumentException('Separator, Enclosure, Escepe parameters shoul be in the end of parameters list');
        }
        if(isset($normalizers[$i])){
            TypeChecker::check($normalizers[$i])->tstring()->fail();
            $this->separator = $normalizers[$i];
        }
        if(isset($normalizers[$i + 1])){
            TypeChecker::check($normalizers[$i])->tstring()->fail();
            $this->enclosure = $normalizers[$i + 1];
        }
        if(isset($normalizers[$i + 2])){
            TypeChecker::check($normalizers[$i])->tstring()->fail();
            $this->escape = $normalizers[$i + 2];
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
    
    protected function validateInputTypes($value)
    {
        TypeChecker::check($value)->tnull()->tscalar()->callback(
            function ($value) {
                return is_object($value) && method_exists($value, '__toString');
            }
        )->inner()->tnull()->tscalar()->callback(
            function ($value) {
                return is_object($value) && method_exists($value, '__toString');
            }
        )->fail();
    }
    
    protected function parseInputValue($value)
    {
        if (is_string($value)) {
            $value = str_getcsv($value, $this->separator, $this->enclosure, $this->escape);
        }
        if (!is_array($value)) {
            $value = (array)$value;
        }
    
        return $value;
    }
}