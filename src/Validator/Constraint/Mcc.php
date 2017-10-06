<?php
/**
 * Project: property-bag
 * User: george
 * Date: 05.10.17
 */

namespace NewInventor\DataStructure\Validator\Constraint;


use NewInventor\DataStructure\Validator\MccValidator;
use Symfony\Component\Validator\Constraint;

class Mcc extends Constraint
{
    public $message = 'Mobile country code "{{ value }}" does not exist.';
    
    public function validatedBy(): string
    {
        return MccValidator::class;
    }
}