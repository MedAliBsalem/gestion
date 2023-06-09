<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Error;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;




class RegistrationController extends AbstractController
{
    private $passwordHasher;
    private $entityManager;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/registration", name="registration")
     */
    public function index(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $error=false;

        if ($form->isSubmitted() && $form->isValid()) {

            
            // Hash the new user's password
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
        
            // Set their role
            $user->setRoles(['ROLE_USER']);
        
            // Save
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        
            return $this->redirectToRoute('app_login');
        } else {
            if ($form->isSubmitted() && !$form->isValid()) {
                        // Handle the case where the form is submitted but not valid
                $error = true;

                // Fetch the form errors
                $formErrors = $form->getErrors(true);

                // Get the first error message from the form
                $errorMessage = '';
                foreach ($formErrors as $error) {
                    $errorMessage = $error->getMessage();
                    break;
                }

                return $this->render('registration/registration.html.twig', [
                    'form' => $form->createView(),
                    'errorMessage' => $errorMessage,
                    'error' => $error
                ]);
            }
        }
        

        return $this->render('registration/registration.html.twig', [
            'form' => $form->createView(),'error' => $error
        ]);
    }
}
