<?php

namespace Oc\Validator\Exception;

use Exception;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends Exception
{
    /**
     * @var ConstraintViolationListInterface
     */
    protected $violations;

    /**
     * @param ConstraintViolationListInterface $violations
     */
    public function __construct(ConstraintViolationListInterface $violations)
    {
        $this->setViolations($violations);

        parent::__construct((string) $this);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $output = '';

        /**
         * @var ConstraintViolationInterface $violation
         */
        foreach ($this->violations as $violation) {
            $output .= $violation->getPropertyPath() . ': ' . $violation->getMessage() . PHP_EOL;
        }

        return $output;
    }

    /**
     * @param ConstraintViolationListInterface $violations
     */
    public function setViolations(ConstraintViolationListInterface $violations)
    {
        $this->violations = $violations;
    }

    /**
     * Returns all violations.
     *
     * @return ConstraintViolationListInterface
     */
    public function getViolations()
    {
        return $this->violations;
    }
}
