<?php

namespace App\Controller;

use App\Entity\UserRequest;
use App\Repository\UserRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

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
    public function updateList(EntityManagerInterface $manager, UserRequest $userRequest, SerializerInterface $serializer): Response
    {
        try {
            $userRequest->setStatusFromUpdate();
            $manager->persist($userRequest);
            $manager->flush();

            /**
             * replacing the json file
             */
            $projectPath = $this->getParameter('kernel.project_dir');
            $isFileReplaced = HomeController::createJsonFile($userRequest, $serializer, $projectPath);
        }catch (\Exception $exception){
            $this->addFlash('error', $exception->getMessage());
        }


        if ($isFileReplaced){
            $this->addFlash('success', 'La modification de la demande est effectuée');
        }else{
            $this->addFlash('danger', 'La modification de la demande est effectuée, mais avec une erreur');
        }

        return $this->redirectToRoute('admin');
    }
}
