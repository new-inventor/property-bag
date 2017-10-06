<?php
/**
 * Project: property-bag
 * User: george
 * Date: 05.10.17
 */

namespace NewInventor\DataStructure\Validator;


use NewInventor\DataStructure\Accessory\MobileCountryCodes;
use NewInventor\DataStructure\Validator\Constraint\Mcc;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class MccValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed          $value      The value that should be validated
     * @param Constraint|Mcc $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!array_key_exists($value, array_flip(MobileCountryCodes::$list))) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}