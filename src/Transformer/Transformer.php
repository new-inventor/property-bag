<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 11.09.17
 */

namespace NewInventor\DataStructure\Transformer;


use NewInventor\DataStructure\Exception\TransformationException;
use NewInventor\TypeChecker\Exception\TypeException;

abstract class Transformer implements TransformerInterface
{
    public static function make(...$config): TransformerInterface
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        return new static(...$config);
    }
    
    /**
     * @param mixed $value
     *
     * @return mixed
     * @throws \NewInventor\DataStructure\Exception\TransformationException
     * @throws \Throwable
     * @throws \NewInventor\TypeChecker\Exception\TypeException
     */
    public function transform($value)
    {
        if ($value === null) {
            return null;
        }
        $this->validateInputTypes($value);
        try {
            return $this->transformInputValue($value);
        } catch (TypeException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new TransformationException(get_class($this), $e, $e->getMessage());
        }
    }
    
    /**
     * @param $value
     *
     * @throws \NewInventor\TypeChecker\Exception\TypeException
     */
    protected function validateInputTypes($value)
    {
    
    }
    
    /**
     * @param $value
     *
     * @return mixed
     */
    protected function transformInputValue($value)
    {
        return $value;
    }
}