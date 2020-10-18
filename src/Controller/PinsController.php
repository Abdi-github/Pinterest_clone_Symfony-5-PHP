<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PinRepository;
use App\Entity\Pin;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\PinType;

class PinsController extends AbstractController {

    /**
     * @Route("/", name="app_home")
     */
    public function index(PinRepository $pinRepo) {




        $pins = $pinRepo->findBy([], ['createdAt' => 'DESC']);

        return $this->render('pins/index.html.twig', compact('pins'));
    }

    /**
     * @Route("/pin/{id<[0-9]+>}", name="app_show")
     */
    public function show(Pin $pin) {
        return $this->render('pins/show.html.twig', compact('pin'));
    }

    /**
     * @Route("/pins/{id<[0-9]+>}/edit",name="app_edit", methods={"POST", "GET"})
     *
     */
    

    public function edit(Request $request, EntityManagerInterface $em, Pin $pin) {


        $form = $this->createForm(PinType::class, $pin);

        $form->handleRequest($request);


        $this->denyAccessUnlessGranted('edit', $pin);



        if ($form->isSubmitted() && $form->isValid()) {


            $em->flush();

            $this->addFlash('success', 'Pin updated successfully');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('pins/edit.html.twig', [
                    'pin' => $pin,
                    'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/pins/add",name="app_add", methods={"POST", "GET"})
     *
     */
    

    public function add(Request $request, EntityManagerInterface $em) {

        $pin = new Pin;

        $form = $this->createForm(PinType::class, $pin);

        $form->handleRequest($request);
        
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');



        if ($form->isSubmitted() && $form->isValid()) {

            $pin->setUser($this->getUser());

            $em->persist($pin);
            $em->flush();

            $this->addFlash('success', 'Pin added successfully');


            return $this->redirectToRoute('app_home');
        }



        return $this->render('pins/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/pins/{id<[0-9]+>}/delete",name="app_delete", methods={"DELETE"})
     *
     */
    public function delete(Request $request, EntityManagerInterface $em, Pin $pin) {

        $this->denyAccessUnlessGranted('delete', $pin);



        if ($this->isCsrfTokenValid('pin_delete_token' . $pin->getId(), $request->request->get('csrf_token'))) {

            $em->remove($pin);


            $em->flush();

            $this->addFlash('danger', 'Pin deleted!');
        }


        return $this->redirectToRoute('app_home');
    }

    
}
