<?php
/**
 * Project: property-bag
 * User: george
 * Date: 04.10.17
 */

namespace NewInventor\DataStructure\Transformer\DataStructure;


use NewInventor\DataStructure\DataStructureInterface;
use NewInventor\DataStructure\Transformer\Property\TransformerInterface;
use NewInventor\TypeChecker\TypeChecker;

class DataStructureTransformer extends AbstractStructureTransformer
{
    /**
     * @param DataStructureInterface $dataStructure
     * @param string                 $propertyName
     * @param TransformerInterface   $transformer
     *
     * @throws \NewInventor\TypeChecker\Exception\TypeException
     * @throws \InvalidArgumentException
     */
    public function transformProperty(&$dataStructure, string $propertyName, TransformerInterface $transformer): void
    {
        if (!TypeChecker::check($dataStructure)->types(DataStructureInterface::class)->result()) {
            throw new \InvalidArgumentException(
                'Data structure in PropertyBagTransformer must implement DataStructureInterface'
            );
        }
        $dataStructure->set($propertyName, $transformer->transform($dataStructure->get($propertyName)));
    }
}