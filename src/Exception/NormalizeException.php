<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 31.08.17
 */

namespace NewInventor\PropertyBag\Exception;


class NormalizeException extends \InvalidArgumentException
{
    /**
     * NormalizeException constructor.
     *
     * @param string     $normalizerClass
     * @param int        $value
     * @param \Throwable $message
     */
    public function __construct(string $normalizerClass, $value, string $message = '')
    {
        parent::__construct(
            "Normalization failed: Normaliser {$normalizerClass} can not normalize value '{$value}'.\n{$message}"
        );
    }
}