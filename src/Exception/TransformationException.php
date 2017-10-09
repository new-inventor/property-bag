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
     * @param string     $message
     */
    public function __construct(string $normalizerClass, \Throwable $previous, $message = '')
    {
        parent::__construct(
            "Transformation failed: Transformer {$normalizerClass} can not normalize value.\n$message",
            0,
            $previous
        );
    }
}