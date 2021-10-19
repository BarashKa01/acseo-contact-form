<?php

namespace App\Controller;

use App\Entity\UserRequest;
use App\Form\UserRequestType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', []);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contactForm(Request $request): Response
    {
        $userRequest = new UserRequest();
        $contactForm = $this->createForm(UserRequestType::class, $userRequest);
        $contactForm->handleRequest($request);

        if ($contactForm->isSubmitted() && $contactForm->isValid()){
            //Perform treatment
            //Create success or error alert
        }

        return $this->render('user_request/form.html.twig', [
            'contact_form' => $contactForm->createView(),
        ]);
    }
}
