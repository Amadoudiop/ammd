<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $missions = $em->getRepository('AppBundle:Mission')->findAll();
        $abouts = $em->getRepository('AppBundle:About')->findAll();
        $actions = $em->getRepository('AppBundle:Action')->findBy(
            [],
            ['createdAt' => 'DESC'],
            6
        );
        $teams = $em->getRepository('AppBundle:Team')->findAll();
        $slides = $em->getRepository('AppBundle:Slide')->findAll();
        $images = $em->getRepository('AppBundle:Media')->findBy(
            [],
            [],
            9
        );
        $pages = $em->getRepository('AppBundle:Page')->findBySection(true);


        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'missions' => $missions,
            'abouts' => $abouts,
            'actions' => $actions,
            'teams' => $teams,
            'slides' => $slides,
            'images' => $images,
            'pages' => $pages,
            'iteration' => 0

        ]);
    }

    /**
     * @Route("/sendEmail", name="sendEmail", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function sendEmailAction(Request $request)
    {
        $name = $request->request->get('name');
        $subject = $request->request->get('sujet');
        $email = $request->request->get('email');
        $message = $request->request->get('message');

        $error = false;

        if( empty( $name ) 
            || empty( $email )
            || empty( $message )
            || empty( $subject )
        ){
            $error['null'] = 'Vous devez remplir tous les champs';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error['email'] = 'vous devez entrer un email valide';
        }


        if(!$error){            
            $mail = (new \Swift_Message($subject))
            ->setFrom($email)
            ->setTo('d.amadoudiop@gmail.com')
            ->setBody(
                $this->renderView(
                    'email/contact.html.twig',
                    ['name' => $name, 'message' => $message]
                ),
                'text/html'
            );      

            $this->get('mailer')->send($mail); 
            $response = ['success' => 'Votre email à été envoyé'];

        }else{
            $response = ['error' => $error];
            //dump($response);die;
        }
        return new JsonResponse($response);


    }
}
