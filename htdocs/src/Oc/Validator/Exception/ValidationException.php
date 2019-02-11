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

    public function __construct(ConstraintViolationListInterface $violations)
    {
        $this->setViolations($violations);

        parent::__construct((string) $this);
    }

    public function __toString(): string
    {
        $output = '';

        /**
         * @var ConstraintViolationInterface
         */
        foreach ($this->violations as $violation) {
            $output .= $violation->getPropertyPath() . ': ' . $violation->getMessage() . PHP_EOL;
        }

        return $output;
    }

    public function setViolations(ConstraintViolationListInterface $violations): void
    {
        $this->violations = $violations;
    }

    /**
     * Returns all violations.
     */
    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}
