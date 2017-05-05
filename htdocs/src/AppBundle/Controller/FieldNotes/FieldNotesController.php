<?php

namespace AppBundle\Controller\FieldNotes;

use AppBundle\Controller\AbstractController;
use AppBundle\Form\UploadFieldNotesType;
use AppBundle\Util\DateUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class FieldNotesController extends AbstractController
{
    /**
     * @Route("/field-notes/", name="field-notes")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();

        $fieldNoteService = $this->get('app.service.field_note');
        $dataProvider = $this->get('app.dataprovider.upload_field_note');

        $repository = $this->getDoctrine()->getRepository('AppBundle:FieldNote');
        $fieldNotes = $repository->findBy(['user' => $user->getId()], ['date' => 'ASC', 'id' => 'ASC']);

        $form = $this->createForm(UploadFieldNotesType::class, $dataProvider->getData($user->getId()));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $form->getData()[UploadFieldNotesType::FIELD_FILE];
            try {
                $ignoreDate = null;
                if (!empty($form->getData()[UploadFieldNotesType::FIELD_IGNORE])) {
                    $ignoreDate = DateUtil::dateTimeFromMySqlFormat($form->getData()[UploadFieldNotesType::FIELD_IGNORE_DATE]);
                }
                $fieldNoteService->importFromFile($file->getRealPath(), $user->getId(), $ignoreDate);
            } catch (\Exception $e) {
                $this->addErrorMessage($e->getMessage());

                return $this->redirectToRoute('field-notes');
            }
            if ($fieldNoteService->hasErrors()) {
                foreach ($fieldNoteService->getErrors() as $error) {
                    $this->addErrorMessage($error);
                }

                return $this->redirectToRoute('field-notes');
            }
            $this->addSuccessMessage(
                $this->get('translator')->trans('field_notes.upload.success')
            );

            return $this->redirectToRoute('field-notes');
        }

        $this->setMenu(MNU_MYPROFILE_FIELD_NOTES);
        $this->setTitle($this->get('translator')->trans('field_notes.field_notes'));

        return $this->render('field-notes/index.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'fieldNotes' => $fieldNotes,
        ]);
    }

    /**
     * @Route("/field-notes/delete/{id}", name="field-notes.delete")
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();

        $repository = $this->getDoctrine()->getRepository('AppBundle:FieldNote');
        $fieldNote = $repository->findOneBy(['user' => $user->getId(), 'id' => $id]);
        if (!$fieldNote) {
            return $this->redirectToRoute('field-notes');
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($fieldNote);
        $em->flush();
        $this->addSuccessMessage(
            $this->get('translator')->trans('field_notes.success.deleted')
        );

        return $this->redirectToRoute('field-notes');
    }

    /**
     * @Route("/field-notes/delete-multiple/", name="field-notes.delete-multiple")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteMultipleAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();

        $selectedFieldNotes = $request->get('selected-field-notes');
        if (!is_array($selectedFieldNotes)) {
            return $this->redirectToRoute('field-notes');
        }

        $repository = $this->getDoctrine()->getRepository('AppBundle:FieldNote');
        $em = $this->getDoctrine()->getManager();
        foreach ($selectedFieldNotes as $fieldNoteId) {
            $fieldNote = $repository->findOneBy(['user' => $user->getId(), 'id' => $fieldNoteId]);
            if (!$fieldNote) {
                continue;
            }
            $em->remove($fieldNote);
        }
        $em->flush();

        $this->addSuccessMessage(
            $this->get('translator')->trans('field_notes.success.deleted_multiple')
        );

        return $this->redirectToRoute('field-notes');
    }
}
