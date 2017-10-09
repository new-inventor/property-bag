<?php
/**
 * Project: property-bag
 * User: george
 * Date: 05.10.17
 */

namespace NewInventor\DataStructure\Transformer;


use NewInventor\DataStructure\Exception\TransformationException;
use NewInventor\TypeChecker\Exception\TypeException;
use NewInventor\TypeChecker\TypeChecker;

class InnerTransformer extends Transformer implements TransformerContainerInterface
{
    /** @var TransformerInterface[][] */
    protected $transformers;
    
    /**
     * ArrayNormalizer constructor.
     *
     * @param array $transformers
     */
    public function __construct(TransformerInterface ...$transformers)
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
                    try {
                        $value[$key] = $transformer->transform($value[$key]);
                    } catch (TypeException $e) {
                        throw new \InvalidArgumentException("Type exception in element $key: \n{$e->getMessage()}");
                    } catch (TransformationException $e) {
                        throw new \InvalidArgumentException(
                            "Transformation exception in element $key: \n{$e->getMessage()}"
                        );
                    }
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