<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Address;
use AppBundle\Form\AddressType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/address")
 */
class AddressController extends Controller
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

        $address = new Address();

        $form = $this->createForm(new AddressType(), $address);
        $address->setContact($contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this
                ->getDoctrine()
                ->getManager();

            $em->persist($address);
            $em->flush();

            return $this->redirectToRoute('app_contact_showid', ['id'=> $id]);
        }
        return ['form' => $form->createView()];
    }
//
//    /**
//     * @Route("/{id}")
//     * @Template()
//     */
//    public function showAction($id)
//    {
//        $addresses = $this->getDoctrine()->getRepository('AppBundle:Contact')->findAll();
//        if($addresses==null) {
//            return $this->redirectToRoute('app_contact_new');
//        }
//        return ['addresses' => $addresses];
//    }

    /**
     * @Route("/{id}/modify/{id_c}")
     * @Template("@App/Address/new.html.twig")
     */
    public function modifyAction(Request $request, $id, $id_c)
    {
        $contact = $this->getDoctrine()->getRepository('AppBundle:Contact')->find($id_c);
        if(!$contact) {
            throw $this->createNotFoundException('Contact not found');
        }
        $address = $this->getDoctrine()->getRepository('AppBundle:Address')->find($id);

        if(!$address) {
            throw $this->createNotFoundException('Address not found');
        }

        $form = $this->createForm(new AddressType(), $address);
        $address->setContact($contact);
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
        $address = $this->getDoctrine()->getRepository('AppBundle:Address')->find($id);
        if(!$address) {
            throw $this->createNotFoundException('Address not found');
        }

        $em = $this
            ->getDoctrine()
            ->getManager();

        $em->remove($address);
        $em->flush();

        return $this->redirectToRoute(
            'app_contact_showid',
            [
                'id' => $id_c
            ]
        );
    }
}
