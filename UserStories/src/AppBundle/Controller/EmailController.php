<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Email;
use AppBundle\Form\EmailType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/email")
 */
class EmailController extends Controller
{
    /**
     * @Route("/new/{id}")
     * @Template()
     */
    public function newAction(Request $request, $id)
    {
        $contact = $this->getDoctrine()->getRepository('AppBundle:Contact')->find($id);
        if(!$contact) {
            throw $this->createNotFoundException('Address not found');
        }

        $email = new Email();
        $form = $this->createForm(new EmailType(), $email);
        $email->setContact($contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this
                ->getDoctrine()
                ->getManager();

            $em->persist($email);
            $em->flush();

            return $this->redirectToRoute('app_contact_showid', ['id'=> $id]);
        }
        return ['form' => $form->createView()];
    }

    /**
     * @Route("/{id}/modify/{id_c}")
     * @Template("@App/Email/new.html.twig")
     */
    public function modifyAction(Request $request, $id, $id_c)
    {
        $contact = $this->getDoctrine()->getRepository('AppBundle:Contact')->find($id_c);
        if(!$contact) {
            throw $this->createNotFoundException('Contact not found');
        }
        $email = $this->getDoctrine()->getRepository('AppBundle:Email')->find($id);

        if(!$email) {
            throw $this->createNotFoundException('Email not found');
        }

        $form = $this->createForm(new EmailType(), $email);
        $email->setContact($contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this
                ->getDoctrine()
                ->getManager();

            $em->flush();

            return $this->redirectToRoute(
                'app_contact_showid',
                [
                    'id' => $id_c
                ]
            );
        }
        return ['form' => $form->createView()];
    }

    /**
     * @Route("/{id}/delete/{id_c}")
     */
    public function deleteAction($id, $id_c)
    {
        $email = $this->getDoctrine()->getRepository('AppBundle:Email')->find($id);
        if(!$email) {
            throw $this->createNotFoundException('Address not found');
        }

        $em = $this
            ->getDoctrine()
            ->getManager();

        $em->remove($email);
        $em->flush();

        return $this->redirectToRoute(
            'app_contact_showid',
            [
                'id' => $id_c
            ]
        );
    }

}
