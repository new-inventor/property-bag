<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 26.09.17
 */

namespace NewInventor\DataStructure\Validator\Constraint;


use NewInventor\DataStructure\Validator\AtListOneOfValidator;
use Symfony\Component\Validator\Constraint;

class AtListOneOf extends Constraint
{
    public $message = 'You should provide at list one of fields: {{ list }} ';
    /** @var array */
    public $list = [];
    
    public function validatedBy(): string
    {
        return AtListOneOfValidator::class;
    }
    
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
    
    public function getRequiredOptions()
    {
        return [
            'list',
        ];
    }
}