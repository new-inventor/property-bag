<?php
/**
 * Project: property-bag
 * User: george
 * Date: 08.10.17
 */

namespace NewInventor\DataStructure;


use NewInventor\DataStructure\Exception\PropertyInvalidTypeException;
use NewInventor\DataStructure\Exception\PropertyTransformationException;
use NewInventor\DataStructure\Exception\TransformationException;
use NewInventor\DataStructure\Transformer\TransformerInterface;
use NewInventor\TypeChecker\Exception\TypeException;

class PropertiesTransformer implements StructureTransformerInterface
{
    /** @var TransformerInterface[] */
    public $transformers = [];
    /** @var array */
    protected $errors = [];
    /** @var bool */
    protected $failOnFirstError = true;
    
    /**
     * DataStructureTransformer constructor.
     *
     * @param TransformerInterface[] $transformers
     */
    public function __construct(array $transformers = [])
    {
        $this->transformers = $transformers;
    }
    
    public function setTransformer(string $propertyName, TransformerInterface $transformer)
    {
        $this->transformers[$propertyName] = $transformer;
        
        return $this;
    }
    
    public function setTransformers(array $transformers)
    {
        foreach ($transformers as $propertyName => $transformer) {
            $this->setTransformer($propertyName, $transformer);
        }
        
        return $this;
    }
    
    public function getTransformer(string $propertyName): ?TransformerInterface
    {
        return $this->transformers[$propertyName] ?? null;
    }
    
    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * @return bool
     */
    public function isFailOnFirstError(): bool
    {
        return $this->failOnFirstError;
    }
    
    /**
     * @param bool $failOnFirstError
     *
     * @return $this
     */
    public function setFailOnFirstError(bool $failOnFirstError)
    {
        $this->failOnFirstError = $failOnFirstError;
        
        return $this;
    }
    
    /**
     * @param array $properties
     *
     * @return array
     * @throws PropertyTransformationException
     * @throws PropertyInvalidTypeException
     */
    public function transform(array $properties = []): array
    {
        $res = [];
        foreach ($properties as $name => $value) {
            try {
                if (
                    array_key_exists($name, $this->transformers) &&
                    in_array(TransformerInterface::class, class_implements($this->transformers[$name]), true)
                ) {
                    $res[$name] = $this->transformers[$name]->transform($value);
                } else {
                    $res[$name] = $value;
                }
            } catch (TypeException $e) {
                if ($this->failOnFirstError) {
                    throw new PropertyInvalidTypeException($name, $e);
                }
                $this->errors[$name]['TYPE_EXCEPTION'] = $e->getMessage();
                $res[$name] = $value;
            } catch (TransformationException $e) {
                if ($this->failOnFirstError) {
                    throw new PropertyTransformationException($name, $e);
                }
                $this->errors[$name]['TRANSFORMATION_EXCEPTION'] = $e->getPrevious()->getMessage();
                $res[$name] = $value;
            }
        }
        
        return $res;
    }
}