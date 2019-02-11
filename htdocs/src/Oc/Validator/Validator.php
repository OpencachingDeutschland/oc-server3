<?php

namespace Oc\Validator;

use Oc\Validator\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Validates a single item.
     *
     * @param mixed $item Item to validate

     * @throws ValidationException
     */
    public function validate($item): void
    {
        $violations = $this->validator->validate($item);

        if ($violations->count() !== 0) {
            throw new ValidationException($violations);
        }
    }
}
