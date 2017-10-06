<?php
/**
 * Project: property-bag
 * User: george
 * Date: 06.10.17
 */

namespace NewInventor\DataStructure\Transformer\DataStructure;


use NewInventor\DataStructure\Transformer\Property\TransformerInterface;
use NewInventor\TypeChecker\TypeChecker;

class ArrayTransformer extends AbstractStructureTransformer
{
    /**
     * @param array                $dataStructure
     * @param string               $propertyName
     * @param TransformerInterface $transformer
     *
     * @throws \NewInventor\TypeChecker\Exception\TypeException
     * @throws \InvalidArgumentException
     */
    public function transformProperty(&$dataStructure, string $propertyName, TransformerInterface $transformer): void
    {
        if (!TypeChecker::check($dataStructure)->tarray()->result()) {
            throw new \InvalidArgumentException('Data structure in ArrayTransformer must be array');
        }
        $dataStructure[$propertyName] = $transformer->transform($dataStructure[$propertyName]);
    }
}