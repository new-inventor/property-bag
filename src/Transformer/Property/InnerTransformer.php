<?php
/**
 * Project: property-bag
 * User: george
 * Date: 05.10.17
 */

namespace NewInventor\DataStructure\Transformer\Property;


use NewInventor\TypeChecker\TypeChecker;

class InnerTransformer extends Transformer
{
    /** @var TransformerInterface[][] */
    protected $transformers;
    
    /**
     * ArrayNormalizer constructor.
     *
     * @param array $transformers
     */
    public function __construct(...$transformers)
    {
        $this->transformers = $transformers;
    }
    
    protected function transformInputValue($value)
    {
        if (!empty($this->transformers)) {
            $i = 0;
            $transformer = $this->transformers[$i];
            foreach ($value as $key => $item) {
                if ($transformer !== null) {
                    $value[$key] = $transformer->transform($value[$key]);
                }
                if (array_key_exists(++$i, $this->transformers)) {
                    $transformer = $this->transformers[$i];
                }
            }
        }
        
        return $value;
    }
    
    protected function validateInputTypes($value)
    {
        TypeChecker::check($value)->tarray()->fail();
    }
}