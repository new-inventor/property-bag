<?php
/**
 * Project: property-bag
 * User: george
 * Date: 06.10.17
 */

namespace NewInventor\DataStructure\Transformer\DataStructure;


use NewInventor\DataStructure\Transformer\Property\TransformerInterface;

interface StructureTransformerInterface
{
    public function setTransformer(string $propertyName, TransformerInterface $transformer);
    
    public function setTransformers(array $transformers);
    
    public function getTransformer(string $propertyName): ?TransformerInterface;
    
    /**
     * @param $dataStructure
     *
     * @throws \NewInventor\DataStructure\Exception\PropertyException
     * @throws \InvalidArgumentException
     */
    public function transform(&$dataStructure): void;
    
    /**
     * @param                      $dataStructure
     * @param string               $propertyName
     * @param TransformerInterface $transformer
     *
     * @throws \NewInventor\TypeChecker\Exception\TypeException
     */
    public function transformProperty(&$dataStructure, string $propertyName, TransformerInterface $transformer): void;
    
    /**
     * @return array
     */
    public function getErrors(): array;
    
    /**
     * @return bool
     */
    public function isFailOnFirstError(): bool;
    
    /**
     * @param bool $failOnFirstError
     */
    public function setFailOnFirstError(bool $failOnFirstError): void;
}