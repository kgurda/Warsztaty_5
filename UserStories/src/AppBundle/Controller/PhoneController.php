<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Phone;
use AppBundle\Form\PhoneType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/phone")
 */
class PhoneController extends Controller
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

        $phone = new Phone();
        $form = $this->createForm(new PhoneType(), $phone);
        $phone->setContact($contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this
                ->getDoctrine()
                ->getManager();

            $em->persist($phone);
            $em->flush();

            return $this->redirectToRoute('app_contact_showid', ['id'=> $id]);
        }
        return ['form' => $form->createView()];
    }

    /**
     * @Route("/{id}/modify/{id_c}")
     * @Template("@App/Phone/new.html.twig")
     */
    public function modifyAction(Request $request, $id, $id_c)
    {
        $contact = $this->getDoctrine()->getRepository('AppBundle:Contact')->find($id_c);
        if(!$contact) {
            throw $this->createNotFoundException('Contact not found');
        }
        $phone = $this->getDoctrine()->getRepository('AppBundle:Phone')->find($id);

        if(!$phone) {
            throw $this->createNotFoundException('Phone not found');
        }

        $form = $this->createForm(new PhoneType(), $phone);
        $phone->setContact($contact);
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
        $phone = $this->getDoctrine()->getRepository('AppBundle:Phone')->find($id);
        if(!$phone) {
            throw $this->createNotFoundException('Phone not found');
        }

        $em = $this
            ->getDoctrine()
            ->getManager();

        $em->remove($phone);
        $em->flush();

        return $this->redirectToRoute(
            'app_contact_showid',
            [
                'id' => $id_c
            ]
        );
    }
}
