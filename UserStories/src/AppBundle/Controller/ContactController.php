<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use AppBundle\Form\ContactType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/contact")
 */
class ContactController extends Controller
{
    /**
     * @Route("/new")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $contact = new Contact();
        $form = $this->createForm(new ContactType(), $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this
                ->getDoctrine()
                ->getManager();

            $em->persist($contact);
            $em->flush();

            return $this->redirectToRoute('app_contact_show');
        }
        return ['form' => $form->createView()];
    }

    /**
     * @Route("/{id}")
     * @Template()
     */
    public function showIdAction($id)
    {
        $contact = $this->getDoctrine()->getRepository('AppBundle:Contact')->find($id);

        return ['contact' => $contact];
    }

    /**
     * @Route("/")
     * @Template()
     */
    public function showAction()
    {
        $contacts = $this->getDoctrine()->getRepository('AppBundle:Contact')->findAll();
        if($contacts==null) {
            return $this->redirectToRoute('app_contact_new');
        }
        return ['contacts' => $contacts];
    }

    /**
     * @Route("/{id}/modify")
     * @Template("@App/Contact/new.html.twig")
     */
    public function modifyAction(Request $request, $id)
    {
        $contact = $this->getDoctrine()->getRepository('AppBundle:Contact')->find($id);

        if(!$contact) {
            throw $this->createNotFoundException('Contact not found');
        }

        $form = $this->createForm(new ContactType(), $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this
                ->getDoctrine()
                ->getManager();

            $em->flush();

            return $this->redirectToRoute(
                'app_contact_show',
                [
                    'id' => $contact->getId()
                ]
            );
        }
        return ['form' => $form->createView()];
    }

    /**
     * @Route("/{id}/delete")
     */
    public function deleteAction($id)
    {
        $contact = $this->getDoctrine()->getRepository('AppBundle:Contact')->find($id);

        if(!$contact) {
            throw $this->createNotFoundException('Contact not found');
        }
        $em = $this
            ->getDoctrine()
            ->getManager();

        $em->remove($contact);
        $em->flush();

        return $this->redirectToRoute('app_contact_show');
    }
}
