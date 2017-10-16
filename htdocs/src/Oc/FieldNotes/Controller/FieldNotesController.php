<?php

namespace Oc\FieldNotes\Controller;

use Oc\AbstractController;
use Oc\FieldNotes\Entity\FieldNote;
use Oc\FieldNotes\FieldNoteService;
use Oc\FieldNotes\Form\DataProvider\UploadFieldNotesDataProvider;
use Oc\FieldNotes\Form\UploadFieldNotesType;
use Oc\Util\DateUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class FieldNotesController
 *
 * @package Oc\FieldNotes\Controller
 */
class FieldNotesController extends AbstractController
{

    /**
     * @var FieldNoteService
     */
    private $fieldNoteService;

    /**
     * @var UploadFieldNotesDataProvider
     */
    private $dataProvider;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * FieldNotesController constructor.
     *
     * @param FieldNoteService $fieldNoteService
     * @param UploadFieldNotesDataProvider $dataProvider
     * @param TranslatorInterface $translator
     */
    public function __construct(
        FieldNoteService $fieldNoteService,
        UploadFieldNotesDataProvider $dataProvider,
        TranslatorInterface $translator
    ) {
        $this->fieldNoteService = $fieldNoteService;
        $this->dataProvider = $dataProvider;
        $this->translator = $translator;
    }

    /**
     * Index action for field-notes.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/field-notes/", name="field-notes")
     */
    public function indexAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();

        $repository = $this->getDoctrine()->getRepository(FieldNote::class);
        $fieldNotes = $repository->findBy([
            'user' => $user->getId()
        ], [
            'date' => 'ASC',
            'id' => 'ASC'
        ]);

        $form = $this->createForm(UploadFieldNotesType::class, $this->dataProvider->getData($user->getId()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var UploadedFile $file
             */
            $file = $form->getData()[UploadFieldNotesType::FIELD_FILE];

            try {
                $ignoreDate = null;

                if (!empty($form->getData()[UploadFieldNotesType::FIELD_IGNORE])) {
                    $ignoreDate = DateUtil::dateTimeFromMySqlFormat(
                        $form->getData()[UploadFieldNotesType::FIELD_IGNORE_DATE]
                    );
                }

                $this->fieldNoteService->importFromFile($file->getRealPath(), $user->getId(), $ignoreDate);
            } catch (\Exception $e) {
                $this->addErrorMessage($e->getMessage());

                return $this->redirectToRoute('field-notes');
            }

            if ($this->fieldNoteService->hasErrors()) {
                foreach ($this->fieldNoteService->getErrors() as $error) {
                    $this->addErrorMessage($error);
                }

                return $this->redirectToRoute('field-notes');
            }

            $this->addSuccessMessage(
                $this->translator->trans('field_notes.upload.success')
            );

            return $this->redirectToRoute('field-notes');
        }

        $this->setMenu(MNU_MYPROFILE_FIELD_NOTES);
        $this->setTitle($this->translator->trans('field_notes.field_notes'));

        return $this->render('field-notes/index.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'fieldNotes' => $fieldNotes
        ]);
    }

    /**
     * Action to delete one field-note.
     *
     * @param int $id
     *
     * @return RedirectResponse
     *
     * @Route("/field-notes/delete/{id}", name="field-notes.delete")
     */
    public function deleteAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();

        $repository = $this->getDoctrine()->getRepository(FieldNote::class);
        $fieldNote = $repository->findOneBy([
            'user' => $user->getId(),
            'id' => $id
        ]);

        if (!$fieldNote) {
            return $this->redirectToRoute('field-notes');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($fieldNote);
        $em->flush();

        $this->addSuccessMessage(
            $this->translator->trans('field_notes.success.deleted')
        );

        return $this->redirectToRoute('field-notes');
    }

    /**
     * Action to delete multiple field-notes.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @Route("/field-notes/delete-multiple/", name="field-notes.delete-multiple")
     */
    public function deleteMultipleAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();

        $selectedFieldNotes = $request->get('selected-field-notes');
        if (!is_array($selectedFieldNotes)) {
            return $this->redirectToRoute('field-notes');
        }

        $repository = $this->getDoctrine()->getRepository(FieldNote::class);
        $em = $this->getDoctrine()->getManager();

        foreach ($selectedFieldNotes as $fieldNoteId) {
            $fieldNote = $repository->findOneBy([
                'user' => $user->getId(),
                'id' => $fieldNoteId
            ]);

            if (!$fieldNote) {
                continue;
            }
            $em->remove($fieldNote);
        }

        $em->flush();

        $this->addSuccessMessage(
            $this->translator->trans('field_notes.success.deleted_multiple')
        );

        return $this->redirectToRoute('field-notes');
    }
}
