<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 10.08.17
 */

namespace NewInventor\PropertyBag\Normalizer;


class ArrayNormalizer extends AbstractNormalizer
{
    /** @var NormalizerInterface[] */
    protected $normalizers;
    
    /**
     * ArrayNormalizer constructor.
     *
     * @param array $normalizers
     */
    public function __construct(...$normalizers)
    {
        $this->normalizers = $normalizers;
    }
    
    protected function parseInputValue($value)
    {
        $value = parent::parseInputValue($value);
        if (!is_array($value)) {
            $value = (array)$value;
        }
    
        return $value;
    }
    
    protected function normalizeInputValue($value)
    {
        if (!empty($this->normalizers)) {
            $i = 0;
            $currentNormalizer = $this->normalizers[$i];
            foreach ($value as $key => $item) {
                if($currentNormalizer !== null){
                    $value[$key] = $currentNormalizer->normalize($item);
                }
                if (array_key_exists(++$i, $this->normalizers)) {
                    $currentNormalizer = $this->normalizers[$i];
                }
            }
        }
        
        return $value;
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
        return static::asString(...$this->normalizers);
    }
}