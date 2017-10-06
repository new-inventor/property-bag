<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 11.09.17
 */

namespace NewInventor\DataStructure\Transformer\Property;


use NewInventor\DataStructure\Exception\TransformationException;
use NewInventor\TypeChecker\Exception\TypeException;

abstract class Transformer implements TransformerInterface
{
    private static $pool = [];
    
    public static function make(...$config): TransformerInterface
    {
        $key = static::asString(...$config);
        if (!isset(self::$pool[$key])) {
            /** @noinspection PhpIncompatibleReturnTypeInspection */
            /** @noinspection PhpMethodParametersCountMismatchInspection */
            self::$pool[$key] = new static(...$config);
        }
    
        return self::$pool[$key];
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
            throw new TransformationException(get_class($this), $e);
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
    
    public static function asString(...$config): string
    {
        return static::class;
    }
    
    public function __toString(): string
    {
        return static::asString();
    }
}