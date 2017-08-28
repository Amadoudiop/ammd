<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\FileHandler;


/**
 * Action controller.
 *
 */
class ActionController extends Controller
{
    /**
     * Lists all action entities.
     *
     * @Route("actions/", name="actions")
     * @Method("GET")
     */
    public function actionsAction(Request $request)
    {
        //$itemPerPage =  (int)$request->query->get('per_page');
        $itemPerPage = 12;
        
        $pagination = 0;

        if( !is_null( $request->query->get('page') ) ){
            $pagination = (int)$request->query->get('page');
        }




        $totalItem = $itemPerPage * $pagination;

        $em = $this->getDoctrine()->getManager();


        $totalActions = $em->getRepository('AppBundle:Action')
                       ->createQueryBuilder('t')
                       ->select('count(t.id)')
                       ->getQuery()->getSingleScalarResult();

        $totalPages = $totalActions / $itemPerPage;


        $actions = $em->getRepository('AppBundle:Action')->findBy(
            [],
            [
                'createdAt' => 'DESC',
            ],
            $itemPerPage,
            $totalItem
        );

        return $this->render('action/list.html.twig', array(
            'actions' => $actions,
            'pagination' => (int)$pagination,
            'totalPages' => (int)$totalPages
        ));
    }

    /**
     * Lists all action entities.
     *
     * @Route("admin/actions/", name="action_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $actions = $em->getRepository('AppBundle:Action')->findAll();

        return $this->render('action/index.html.twig', array(
            'actions' => $actions,
        ));
    }

    /**
     * Creates a new action entity.
     *
     * @Route("admin/action/new", name="action_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $action = new Action();

        $form = $this->createForm('AppBundle\Form\ActionType', $action);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();


            // $file stores the uploaded PDF file
            $file = $action->getMedia()->getThumb();

            if( $file != null )
            {

                $fileHandler = $this->container->get('file_handler');
                $fileName = $fileHandler->upload($file,  $this->getParameter('simple_directory'));
                
                $action->getMedia()->setExtension($fileName['extension']);
                $action->getMedia()->setName($fileName['name']);
                $action->getMedia()->setThumb($fileName['thumb']);

            }

            $action->setCreatedAt(new \DateTime('now'));
            $em->persist($action);
            $em->flush();

            return $this->redirectToRoute('action_show', array('link' => $action->getLink()));
        }

        return $this->render('action/new.html.twig', array(
            'action' => $action,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a action entity.
     *
     * @Route("action/{link}", name="action_show")
     * @Method("GET")
     */
    public function showAction(Action $action)
    {
        $deleteForm = $this->createDeleteForm($action);
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Action');

        $query = $repository->createQueryBuilder('p')
            ->where('p.createdAt > :createdAt')
            ->setParameter('createdAt', $action->getCreatedAt())
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        $actionAfter = $query->getResult();
        if( array_key_exists( 0, $actionAfter ) ){
            $actionAfter = $actionAfter[0];
        }else{
            $actionAfter = null;
        }

        $query2 = $repository->createQueryBuilder('p')
            ->where('p.createdAt < :createdAt')
            ->setParameter('createdAt', $action->getCreatedAt())
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        $actionBefore = $query2->getResult();
        if( array_key_exists( 0, $actionBefore ) ){
            $actionBefore = $actionBefore[0];
        }else{
            $actionBefore = null;
        }

        /*dump($actionAfter);
        dump($action);
        dump($actionBefore);
        die;*/


        return $this->render('action/show.html.twig', array(
            'action' => $action,
            'actionBefore' => $actionBefore,
            'actionAfter' => $actionAfter,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing action entity.
     *
     * @Route("admin/action/{link}/edit", name="action_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Action $action)
    {
        $deleteForm = $this->createDeleteForm($action);
        $editForm = $this->createForm('AppBundle\Form\ActionType', $action);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {


            // $file stores the uploaded PDF file
            $file = $action->getMedia()->getThumb();

            if( $file != null )
            {

                $fileHandler = $this->container->get('file_handler');
                $fileName = $fileHandler->upload($file,  $this->getParameter('simple_directory'));
                
                $action->getMedia()->setExtension($fileName['extension']);
                $action->getMedia()->setName($fileName['name']);
                $action->getMedia()->setThumb($fileName['thumb']);

            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('action_edit', array('link' => $action->getLink()));
        }

        return $this->render('action/edit.html.twig', array(
            'action' => $action,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a action entity.
     *
     * @Route("admin/action/{id}", name="action_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Action $action)
    {
        $form = $this->createDeleteForm($action);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($action);
            $em->flush();
        }

        return $this->redirectToRoute('action_index');
    }

    /**
     * Creates a form to delete a action entity.
     *
     * @param Action $action The action entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Action $action)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('action_delete', array('id' => $action->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
