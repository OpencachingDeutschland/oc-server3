<?php

namespace Oc\FieldNotes\Import;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Oc\FieldNotes\Context\HandleFormContext;
use Oc\FieldNotes\Exception\FileFormatException;
use Oc\FieldNotes\Form\UploadFormData;
use Oc\FieldNotes\Import\Context\ImportContext;
use Oc\Validator\Exception\ValidationException;
use Oc\Validator\Validator;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\Node\Node;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

class ImportService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var FileParser
     */
    private $fileParser;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var Importer
     */
    private $importer;

    public function __construct(
        Importer $importer,
        FileParser $fileParser,
        Validator $validator,
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
        $this->fileParser = $fileParser;
        $this->validator = $validator;
        $this->importer = $importer;
    }

    /**
     * Handles submitted form data.
     */
    public function handleFormData(UploadFormData $formData): HandleFormContext
    {
        $success = true;
        $errors = [];

        try {
            $fieldNotes = $this->fileParser->parseFile($formData->file);
        } catch (FileFormatException $e) {
            $errors[] = $this->translator->trans('field_notes.error.wrong_file_format');
            $success = false;
        }

        try {
            $this->validator->validate($fieldNotes);
        } catch (ValidationException $e) {
            /**
             * @var ConstraintViolationInterface
             */
            foreach ($e->getViolations() as $violation) {
                $linePrefix = $this->getTranslatedLinePrefix($violation);

                unset($fieldNotes[$this->getLineNumber($violation)]);

                $errors[] = sprintf(
                    '%s %s',
                    $linePrefix,
                    $violation->getMessage()
                );
            }
            $success = false;
        } catch (Exception $e) {
            $errors[] = $this->translator->trans('general.error.unknown_error');
            $success = false;
        }

        if (is_array($fieldNotes)) {
            $context = new ImportContext($fieldNotes, $formData);

            $this->importer->import($context);
        }

        return new HandleFormContext($success, $errors);
    }

    /**
     * Fetches the line of the constraint violation and returns the line prefix with line number.
     */
    private function getTranslatedLinePrefix(ConstraintViolationInterface $violation): string
    {
        $line = $this->getLineNumber($violation) + 1;

        $linePrefix = $this->translator->trans(
            'field_notes.error.line_prefix',
            [
                '%line%' => $line,
            ]
        );

        return $linePrefix;
    }

    private function getLineNumber(ConstraintViolationInterface $violation): int
    {
        /**
         * @var Node
         */
        $expressionAst = (new ExpressionLanguage())->parse($violation->getPropertyPath(), [])->getNodes();

        return (int) $expressionAst->nodes['node']->nodes[1]->attributes['value'];
    }
}
