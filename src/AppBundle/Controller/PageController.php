<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Page controller.
 *
 */
class PageController extends Controller
{
    /**
     * Lists all page entities.
     *
     * @Route("admin/pages/", name="page_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $pages = $em->getRepository('AppBundle:Page')->findAll();

        return $this->render('page/index.html.twig', array(
            'pages' => $pages,
        ));
    }

    /**
     * Creates a new page entity.
     *
     * @Route("admin/page/new", name="page_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $page = new Page();
        $form = $this->createForm('AppBundle\Form\PageType', $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $file = $page->getMedia()->getThumb();

            if( $file != null )
            {

                $fileHandler = $this->container->get('file_handler');
                $fileName = $fileHandler->upload($file,  $this->getParameter('simple_directory'));
                
                $page->getMedia()->setExtension($fileName['extension']);
                $page->getMedia()->setName($fileName['name']);
                $page->getMedia()->setThumb($fileName['thumb']);

            }

            $em->persist($page);
            $em->flush();

            return $this->redirectToRoute('page_show', array('link' => $page->getLink()));
        }

        return $this->render('page/new.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a page entity.
     *
     * @Route("page/{link}", name="page_show")
     * @Method("GET")
     */
    public function showAction(Page $page)
    {
        $deleteForm = $this->createDeleteForm($page);

        return $this->render('page/show.html.twig', array(
            'page' => $page,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing page entity.
     *
     * @Route("admin/page/{link}/edit", name="page_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Page $page)
    {
        $deleteForm = $this->createDeleteForm($page);
        $editForm = $this->createForm('AppBundle\Form\PageType', $page);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $file = $page->getMedia()->getThumb();

            if( $file != null )
            {

                $fileHandler = $this->container->get('file_handler');
                $fileName = $fileHandler->upload($file,  $this->getParameter('simple_directory'), 570, 400);
                
                $page->getMedia()->setExtension($fileName['extension']);
                $page->getMedia()->setName($fileName['name']);
                $page->getMedia()->setThumb($fileName['thumb']);

            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('page_edit', array('link' => $page->getLink()));
        }

        return $this->render('page/edit.html.twig', array(
            'page' => $page,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a page entity.
     *
     * @Route("admin/page/{id}", name="page_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Page $page)
    {
        $form = $this->createDeleteForm($page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($page);
            $em->flush();
        }

        return $this->redirectToRoute('page_index');
    }

    /**
     * Creates a form to delete a page entity.
     *
     * @param Page $page The page entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Page $page)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('page_delete', array('id' => $page->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
