<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PinRepository;
use App\Entity\Pin;
//use Symfony\Component\Form\Extension\Core\Type\TextType;
//use Symfony\Component\Form\Extension\C ore\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\PinType;

class PinsController extends AbstractController {

    /**
     * @Route("/", name="app_home")
     */
    public function index(PinRepository $pinRepo) {


//       $pins = $pinRepo->findAll();
        /*
         * To display the pins by order of creation 
         * (The Last entered the first displayed) we change
         * the above findAll() method to findBy() method
         */

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
    /*
      public function edit(Request $request, EntityManagerInterface $em, Pin $pin) {


      $form = $this->createFormBuilder($pin)
      ->add('title',TextType::class*)
      ->add('description' ,TextareaType::class)
      ->getForm()
      ;

      $form->handleRequest($request);


      if ($form->isSubmitted() && $form->isValid()) {


      $em->flush();

      return $this->redirectToRoute('home');
      //            return $this->redirectToRoute('pin.show', ['id' => $pin->getId()]);
      }

      return $this->render('pins/edit.html.twig', [
      'pin' => $pin,
      'editForm' => $form->createView()
      ]);
      }

     */

    /*
     *  To simplify our code in edit function we can create form type on console
     *  by make:form command and we create PinType.php on form directory and 
     *  the edit function will be simplified as below
     */

    public function edit(Request $request, EntityManagerInterface $em, Pin $pin) {


        $form = $this->createForm(PinType::class, $pin);

        $form->handleRequest($request);


        $this->denyAccessUnlessGranted('edit', $pin);



        if ($form->isSubmitted() && $form->isValid()) {


            $em->flush();

            $this->addFlash('success', 'Pin updated successfully');

            return $this->redirectToRoute('app_home');
//            return $this->redirectToRoute('pin.show', ['id' => $pin->getId()]);
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
    /*
      public function add(Request $request, EntityManagerInterface $em) {

      $pin = new Pin;

      $form = $this->createFormBuilder($pin)
      ->add('title', TextType::class)
      ->add('description', TextareaType::class)
      ->getForm()
      ;

      $form->handleRequest($request);


      if ($form->isSubmitted() && $form->isValid()) {


      $em->persist($pin);
      $em->flush();

      return $this->redirectToRoute('home');
      //            return $this->redirectToRoute('pin.show', ['id' => $pin->getId()]);
      }



      return $this->render('pins/add.html.twig', ['addForm' => $form->createView()]);
      }

     */



    /*
     *  To simplify our code in add function we can create form type on console
     *  by make:form command and we create PinType.php on form directory and 
     *  the add function will be simplified as below
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
//            return $this->redirectToRoute('pin.show', ['id' => $pin->getId()]);
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
//            return $this->redirectToRoute('pin.show', ['id' => $pin->getId()]);
    }

    /*
     * The above delete function works but we should use DELETE method instead of
     * GET method so we should change the delete button in show.html.twig to form as shown
     */
}
