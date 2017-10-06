<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 26.09.17
 */

namespace NewInventor\DataStructure\Validator;


use NewInventor\DataStructure\Validator\Constraint\AtListOneOf;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AllOfValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param object                 $value      The value that should be validated
     * @param AtListOneOf|Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        $list = [];
        if (is_callable($constraint->list)) {
            $list = call_user_func($constraint->list);
        }
        if (method_exists($value, 'toArray')) {
            $data = array_intersect_key($value->toArray(), array_flip($list));
            $data = array_filter($data);
            if (count($data) !== count($list)) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->setParameter('{{ list }}', implode(', ', $list))
                    ->atPath('*')
                    ->addViolation();
            }
        }
    }
}