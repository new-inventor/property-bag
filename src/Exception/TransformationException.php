<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 31.08.17
 */

namespace NewInventor\DataStructure\Exception;


class TransformationException extends \InvalidArgumentException
{
    /**
     * NormalizeException constructor.
     *
     * @param string     $normalizerClass
     * @param \Throwable $previous
     */
    public function __construct(string $normalizerClass, \Throwable $previous)
    {
        parent::__construct(
            "Normalization failed: Normalizer {$normalizerClass} can not normalize value",
            0,
            $previous
        );
    }
}