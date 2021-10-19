<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Cassandra\Type\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $userRepository;
    private $passwordEncoder;

    public function __constructor(UserRepository $userRepository, PasswordEncoderInterface $passwordEncoder){
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route ("register", name="user_register")
     * @param Request $request
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $loginFormAuthenticator
     * @return Response|null
     * @throws \Exception
     */
    public function register(Request $request, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $loginFormAuthenticator): ?Response
    {
        $user = new User();
        $register_form = $this->createForm(UserType::class, $user);
        $register_form->handleRequest($request);
        if ($register_form->isSubmitted() && $register_form->isValid())
        {
            $password = $user->getPassword();
            $user = $user->setPassword($this->passwordEncoder->encodePassword($user, $password));

            /**
             * Because different users can have the same username, authentication should be handled by email
             */
            if (!$this->userRepository->findOneBy(['email' => $user->getEmail()]))
            {
                $user->setRoles(['ROLE_USER']);
                $this->om->persist($user);
                $this->om->flush();

                return $guardHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $loginFormAuthenticator,
                    'login_admin'
                );
            }
            else
            {
                $this->addFlash('error', 'Cet e-mail est déjà utilisé, avez-vous oublié votre mot de passe ?');
            }
        }
        return $this->render('admin/user/new.html.twig', [
            'user' => $user,
            'register_form' => $register_form->createView()
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
