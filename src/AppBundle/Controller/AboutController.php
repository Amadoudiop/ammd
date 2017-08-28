<?php

namespace AppBundle\Controller;

use AppBundle\Entity\About;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * About controller.
 *
 * @Route("admin/about")
 */
class AboutController extends Controller
{
    /**
     * Lists all about entities.
     *
     * @Route("/", name="about_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $abouts = $em->getRepository('AppBundle:About')->findAll();

        return $this->render('about/index.html.twig', array(
            'abouts' => $abouts,
        ));
    }

    /**
     * Creates a new about entity.
     *
     * @Route("/new", name="about_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $about = new About();
        $form = $this->createForm('AppBundle\Form\AboutType', $about);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $file = $about->getMedia()->getThumb();

            if( $file != null )
            {

                $fileHandler = $this->container->get('file_handler');
                $fileName = $fileHandler->upload($file,  $this->getParameter('simple_directory'));
                
                $about->getMedia()->setExtension($fileName['extension']);
                $about->getMedia()->setName($fileName['name']);
                $about->getMedia()->setThumb($fileName['name']);

            }

            $em->persist($about);
            $em->flush();

            return $this->redirectToRoute('about_show', array('id' => $about->getId()));
        }

        return $this->render('about/new.html.twig', array(
            'about' => $about,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a about entity.
     *
     * @Route("/{id}", name="about_show")
     * @Method("GET")
     */
    public function showAction(About $about)
    {
        $deleteForm = $this->createDeleteForm($about);

        return $this->render('about/show.html.twig', array(
            'about' => $about,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing about entity.
     *
     * @Route("/{id}/edit", name="about_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, About $about)
    {
        $deleteForm = $this->createDeleteForm($about);
        $editForm = $this->createForm('AppBundle\Form\AboutType', $about);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $file = $about->getMedia()->getThumb();

            if( $file != null )
            {

                $fileHandler = $this->container->get('file_handler');
                $fileName = $fileHandler->upload($file,  $this->getParameter('simple_directory'));
                
                $about->getMedia()->setExtension($fileName['extension']);
                $about->getMedia()->setName($fileName['name']);
                $about->getMedia()->setThumb($fileName['name']);

            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('about_edit', array('id' => $about->getId()));
        }

        return $this->render('about/edit.html.twig', array(
            'about' => $about,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a about entity.
     *
     * @Route("/{id}", name="about_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, About $about)
    {
        $form = $this->createDeleteForm($about);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($about);
            $em->flush();
        }

        return $this->redirectToRoute('about_index');
    }

    /**
     * Creates a form to delete a about entity.
     *
     * @param About $about The about entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(About $about)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('about_delete', array('id' => $about->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
