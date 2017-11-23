<?php

namespace Oc\FieldNotes\Controller;

use Oc\AbstractController;
use Oc\FieldNotes\Form\UploadFormDataFactory;
use Oc\FieldNotes\Form\UploadType;
use Oc\FieldNotes\Import\ImportService;
use Oc\FieldNotes\Persistence\FieldNoteService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class FieldNotesController
 */
class FieldNotesController extends AbstractController
{
    /**
     * @var ImportService
     */
    private $importService;

    /**
     * @var FieldNoteService
     */
    private $fieldNoteService;

    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var UploadFormDataFactory
     */
    private $formDataFactory;

    /**
     * FieldNotesController constructor.
     *
     * @param ImportService $importService
     * @param FieldNoteService $fieldNoteService
     * @param UploadFormDataFactory $formDataFactory
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ImportService $importService,
        FieldNoteService $fieldNoteService,
        UploadFormDataFactory $formDataFactory,
        TranslatorInterface $translator
    ) {
        $this->importService = $importService;
        $this->fieldNoteService = $fieldNoteService;
        $this->formDataFactory = $formDataFactory;
        $this->translator = $translator;
    }

    /**
     * Index action for field-notes.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/field-notes/", name="field_notes.index")
     */
    public function indexAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $this->setMenu(MNU_MYPROFILE_FIELD_NOTES);
        $this->setTitle($this->translator->trans('field_notes.field_notes'));

        $user = $this->getUser();

        $fieldNotes = $this->fieldNoteService->getUserListing($user->getId());

        $fieldNoteFormData = $this->formDataFactory->create($user->getId());

        $form = $this->createForm(UploadType::class, $fieldNoteFormData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formHandleContext = $this->importService->handleFormData($fieldNoteFormData);

            if (!$formHandleContext->isSuccess()) {
                foreach ($formHandleContext->getErrors() as $error) {
                    $this->addErrorMessage($error);
                }

                return $this->redirectToRoute('field_notes.index');
            }

            $this->addSuccessMessage(
                $this->translator->trans('field_notes.upload.success')
            );

            return $this->redirectToRoute('field_notes.index');
        }

        return $this->render('field-notes/index.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'fieldNotes' => $fieldNotes,
        ]);
    }

    /**
     * Action to delete one field-note.
     *
     * @param int $id
     *
     * @return RedirectResponse
     *
     * @Route("/field-notes/delete/{id}", name="field_notes.delete")
     */
    public function deleteAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();

        $fieldNote = $this->fieldNoteService->fetchOneBy([
            'id' => $id,
            'user_id' => $user->getId(),
        ]);

        if ($fieldNote === null) {
            return $this->redirectToRoute('field_notes.index');
        }

        $this->fieldNoteService->remove($fieldNote);

        $this->addSuccessMessage(
            $this->translator->trans('field_notes.success.deleted')
        );

        return $this->redirectToRoute('field_notes.index');
    }

    /**
     * Action to delete multiple field-notes.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @Route("/field-notes/delete-multiple/", name="field_notes.delete_multiple")
     */
    public function deleteMultipleAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();

        $selectedFieldNotes = $request->get('selected-field-notes');
        if (!is_array($selectedFieldNotes)) {
            return $this->redirectToRoute('field_notes.index');
        }

        foreach ($selectedFieldNotes as $fieldNoteId) {
            $fieldNote = $this->fieldNoteService->fetchOneBy([
                'id' => $fieldNoteId,
                'user_id' => $user->getId(),
            ]);

            if ($fieldNote === null) {
                continue;
            }

            $this->fieldNoteService->remove($fieldNote);
        }

        $this->addSuccessMessage(
            $this->translator->trans('field_notes.success.deleted_multiple')
        );

        return $this->redirectToRoute('field_notes.index');
    }
}
