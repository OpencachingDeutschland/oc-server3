<?php

namespace AppBundle\Controller\FieldNotes;

use AppBundle\Controller\AbstractController;
use AppBundle\Form\UploadFieldNotesType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class FieldNotesController extends AbstractController
{
    /**
     * @Route("/field-notes/", name="field-notes")
     *
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Unable to access this page!');
        $user = $this->getUser();

        $repository = $this->getDoctrine()->getRepository('AppBundle:FieldNote');
        $fieldNotes = $repository->findBy(['user' => $user->getId()], ['date' => 'ASC']);

        $form = $this->createForm(UploadFieldNotesType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $form->getData()[UploadFieldNotesType::FIELD_FILE];
            $fieldNoteService = $this->get('app.service.field_note');
            try {
                $fieldNoteService->importFromFile($file->getRealPath(), $user->getId());
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
            $this->addSuccessMessage($this->get('translator')->trans('Field Notes successfully uploaded.'));

            return $this->redirectToRoute('field-notes');
        }

        $this->setMenu(MNU_MYPROFILE_FIELD_NOTES);
        $this->setTitle($this->get('translator')->trans('Field Notes'));

        return $this->render('field-notes/index.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'fieldNotes' => $fieldNotes,
        ]);
    }

    /**
     * @Route("/field-notes/delete/{id}", name="field-notes.delete")
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse;
     */
    public function deleteAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Unable to access this page!');
        $user = $this->getUser();

        $repository = $this->getDoctrine()->getRepository('AppBundle:FieldNote');
        $fieldNote = $repository->findOneBy(['user' => $user->getId(), 'id' => $id]);
        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($fieldNote);
        $em->flush();
        $this->addSuccessMessage($this->get('translator')->trans('Field note deleted.'));

        return $this->redirectToRoute('field-notes');
    }
}
