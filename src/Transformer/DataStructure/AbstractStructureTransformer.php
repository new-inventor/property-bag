<?php
/**
 * Project: property-bag
 * User: george
 * Date: 06.10.17
 */

namespace NewInventor\DataStructure\Transformer\DataStructure;


use NewInventor\DataStructure\DataStructureInterface;
use NewInventor\DataStructure\Exception\PropertyInvalidTypeException;
use NewInventor\DataStructure\Exception\PropertyTransformationException;
use NewInventor\DataStructure\Exception\TransformationException;
use NewInventor\DataStructure\Transformer\Property\TransformerInterface;
use NewInventor\TypeChecker\Exception\TypeException;

abstract class AbstractStructureTransformer implements StructureTransformerInterface
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
     */
    public function setFailOnFirstError(bool $failOnFirstError): void
    {
        $this->failOnFirstError = $failOnFirstError;
    }
    
    /**
     * @param DataStructureInterface $dataStructure
     *
     * @throws \NewInventor\DataStructure\Exception\PropertyException
     * @throws \InvalidArgumentException
     */
    public function transform(&$dataStructure): void
    {
        foreach ($this->transformers as $propertyName => $transformer) {
            try {
                if ($transformer === null) {
                    continue;
                }
                $this->transformProperty($dataStructure, $propertyName, $transformer);
            } catch (TypeException $e) {
                if ($this->failOnFirstError) {
                    throw new PropertyInvalidTypeException($propertyName, $e);
                }
                $this->errors[$propertyName]['TYPE_EXCEPTION'] = $e->getMessage();
            } catch (TransformationException $e) {
                if ($this->failOnFirstError) {
                    throw new PropertyTransformationException($propertyName, $e);
                }
                $this->errors[$propertyName]['TRANSFORMATION_EXCEPTION'] = $e->getPrevious()->getMessage();
            }
        }
    }
    
    /**
     * @param                      $dataStructure
     * @param string               $propertyName
     * @param TransformerInterface $transformer
     *
     * @throws \NewInventor\TypeChecker\Exception\TypeException
     */
    abstract public function transformProperty(
        &$dataStructure,
        string $propertyName,
        TransformerInterface $transformer
    ): void;
}