<?php

namespace App\Controller;

use App\Entity\UserRequest;
use App\Repository\UserRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    private $requestRepository;

    public function __construct(UserRequestRepository $requestRepository)
    {
        $this->requestRepository = $requestRepository;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function admin(): Response
    {
        $requests = $this->requestRepository->findAllSorted();
        //dd($requests);

        return $this->render('admin/index.html.twig', [
            'requests' => $requests,
        ]);
    }

    /**
     * @Route("/update-list/{id}", name="update.list", methods="POST")
     */
    public function updateList(EntityManagerInterface $manager, UserRequest $userRequest)
    {
        $userRequest->setStatusFromUpdate();
        $manager->persist($userRequest);
        $manager->flush();
        return $this->redirectToRoute('admin');
    }
}
