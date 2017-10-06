<?php
/**
 * Project: property-bag
 * User: george
 * Date: 05.10.17
 */

namespace NewInventor\DataStructure\Transformer\Property;


class ChainTransformer extends Transformer
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
            foreach ($this->transformers as $transformer) {
                $value = $transformer->transform($value);
            }
        }
        
        return $value;
    }
}