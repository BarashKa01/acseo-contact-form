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

                $projectPath = $this->getParameter('kernel.project_dir');
                $isFileCreated = $this->createJsonFile($userRequest, $serializer, $projectPath);

                if ($isFileCreated){
                    $this->addFlash('success', 'Votre demande est envoyée');
                }else{
                    $this->addFlash('danger', 'Votre demande est envoyée, mais avec une erreur');
                }

            }catch (\Exception $e){
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->render('user_request/form.html.twig', [
            'contact_form' => $contactForm->createView(),
        ]);
    }

    public static function createJsonFile(UserRequest $userRequest, SerializerInterface $serializer, string $projectPath):bool
    {
        $fs = new Filesystem();
        $tmpFile = $fs->tempnam('/temp', 'temp_'.$userRequest->getId().'.json');

        /**
         * Prepare the file name, path and path separator
         */
        $jsonDirectoryPath = $projectPath.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."JSONuserRequests".DIRECTORY_SEPARATOR;
        $fileName = $userRequest->getId();

        $fullNameWithPath = $jsonDirectoryPath.$fileName.".json";

        if (!$fs->exists($fullNameWithPath)) {
            $fs->mkdir($jsonDirectoryPath, 0755);
        }

        $jsonData = $serializer->serialize($userRequest, 'json');

        try {
            $fs->appendToFile($tmpFile, $jsonData);
            $fs->rename($tmpFile, $fullNameWithPath, true);
            return true;

        }catch (IOException $exception){
            return false;
        }
    }
}
