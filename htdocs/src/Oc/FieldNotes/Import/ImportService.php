<?php

namespace Oc\FieldNotes\Import;

use Exception;
use Oc\FieldNotes\Context\HandleFormContext;
use Oc\FieldNotes\Exception\FileFormatException;
use Oc\FieldNotes\Form\UploadFormData;
use Oc\FieldNotes\Import\Context\ImportContext;
use Doctrine\ORM\EntityManagerInterface;
use Oc\Validator\Exception\ValidationException;
use Oc\Validator\Validator;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\Node\Node;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Class ImportService
 *
 * @package Oc\FieldNotes\Import
 */
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

    /**
     * ImportService constructor.
     *
     * @param Importer $importer
     * @param FileParser $fileParser
     * @param Validator $validator
     * @param TranslatorInterface $translator
     */
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
     *
     * @param UploadFormData $formData
     *
     * @return HandleFormContext
     */
    public function handleFormData(UploadFormData $formData)
    {
        $success = false;
        $errors = [];

        try {
            $fieldNotes = $this->fileParser->parseFile($formData->file);

            $this->validator->validate($fieldNotes);

            $context = new ImportContext($fieldNotes, $formData);

            $this->importer->import($context);

            $success = true;
        } catch (FileFormatException $e) {
            $errors[] = $this->translator->trans('field_notes.error.wrong_file_format');
        } catch (ValidationException $e) {
            /**
             * @var ConstraintViolationInterface $violation
             */
            foreach ($e->getViolations() as $violation) {
                $linePrefix = $this->getTranslatedLinePrefix($violation);

                $errors[] = sprintf(
                    '%s %s',
                    $linePrefix,
                    $violation->getMessage()
                );
            }
        } catch (Exception $e) {
            $errors[] = $this->translator->trans('general.error.unknown_error');
        }

        return new HandleFormContext($success, $errors);
    }

    /**
     * Fetches the line of the constraint violation and returns the line prefix with line number.
     *
     * @param ConstraintViolationInterface $violation
     *
     * @return string
     */
    private function getTranslatedLinePrefix(ConstraintViolationInterface $violation)
    {
        /**
         * @var Node $expressionAst
         */
        $expressionAst = (new ExpressionLanguage())->parse($violation->getPropertyPath(), [])->getNodes();

        $line = ((int)$expressionAst->nodes['node']->nodes[1]->attributes['value']) + 1;

        $linePrefix = $this->translator->trans('field_notes.error.line_prefix', [
            '%line%' => $line
        ]);

        return $linePrefix;
    }
}
