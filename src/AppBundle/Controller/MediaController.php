<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Media controller.
 *
 * @Route("admin/media")
 */
class MediaController extends Controller
{
    /**
     * Lists all media entities.
     *
     * @Route("s/", name="media_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $media = $em->getRepository('AppBundle:Media')->findAll();

        return $this->render('media/index.html.twig', array(
            'media' => $media,
        ));
    }

    /**
     * Creates a new media entity.
     *
     * @Route("/new", name="media_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $media = new Media();
        $form = $this->createForm('AppBundle\Form\MediaType', $media);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($media);
            $em->flush();

            return $this->redirectToRoute('media_show', array('id' => $media->getId()));
        }

        return $this->render('media/new.html.twig', array(
            'media' => $media,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a media entity.
     *
     * @Route("/{id}", name="media_show")
     * @Method("GET")
     */
    public function showAction(Media $media)
    {
        $deleteForm = $this->createDeleteForm($media);

        return $this->render('media/show.html.twig', array(
            'media' => $media,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing media entity.
     *
     * @Route("/{id}/edit", name="media_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Media $media)
    {
        $deleteForm = $this->createDeleteForm($media);
        $editForm = $this->createForm('AppBundle\Form\MediaType', $media);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('media_edit', array('id' => $media->getId()));
        }

        return $this->render('media/edit.html.twig', array(
            'media' => $media,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a media entity.
     *
     * @Route("/{id}", name="media_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Media $media)
    {
        $form = $this->createDeleteForm($media);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($media);
            $em->flush();
        }

        return $this->redirectToRoute('media_index');
    }

    /**
     * Creates a form to delete a media entity.
     *
     * @param Media $media The media entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Media $media)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('media_delete', array('id' => $media->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
