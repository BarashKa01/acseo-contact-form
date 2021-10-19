<?php

namespace App\Controller;

use App\Entity\UserRequest;
use App\Form\UserRequestType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

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
    public function contactForm(EntityManagerInterface $manager, Request $request, SerializerInterface $serializer): Response
    {
        $userRequest = new UserRequest();
        $contactForm = $this->createForm(UserRequestType::class, $userRequest);
        $contactForm->handleRequest($request);

        if ($contactForm->isSubmitted() && $contactForm->isValid()){
            try {
                $userRequest->setStatus(false);
                $manager->persist($userRequest);
                $manager->flush();

                $isFileCreated = $this->createJsonFile($userRequest, $serializer);

                if ($isFileCreated){
                    $this->addFlash('success', 'Votre demande est envoyée');
                }else{
                    $this->addFlash('error', 'Votre demande est envoyée, mais avec une erreur');
                }

            }catch (\Exception $e){
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->render('user_request/form.html.twig', [
            'contact_form' => $contactForm->createView(),
        ]);
    }

    private function createJsonFile(UserRequest $userRequest, SerializerInterface $serializer):bool
    {
        $fs = new Filesystem();
        $tmpFile = $fs->tempnam('/temp', 'temp_'.$userRequest->getUsermail().'.json');

        /**
         * Prepare the file name and path
         */
        $date = new \DateTime('now');
        $date = $date->format("Y-m-d-H\hi\ms\s");
        $jsonDirectoryPath = $this->getParameter('kernel.project_dir')."\\src\\JSONuserRequests\\";
        $fileName = $userRequest->getUsermail().'-'.$date;

        $fullNameWithPath = $jsonDirectoryPath.$fileName.".json";

        if (!$fs->exists($fullNameWithPath)) {
            $fs->mkdir($jsonDirectoryPath, 0755);
        }

        $jsonData = $serializer->serialize($userRequest, 'json');

        try {
            $fs->appendToFile($tmpFile, $jsonData);
            $fs->rename($tmpFile, $fullNameWithPath);
            return true;

        }catch (IOException $exception){
            return false;
        }
    }
}
