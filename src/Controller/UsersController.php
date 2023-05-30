<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;


class UsersController extends AbstractController
{
    private $passwordHasher;
    private $entityManager;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    #[Route('/users', name: 'display_users')]
    public function index(ManagerRegistry $registry): Response
    {
        $entityManager = $registry->getManager();
        $UserRepository = $entityManager->getRepository(User::class);
        $users = $UserRepository->findAll();
        return $this->render('users/listUser.html.twig', [
            'u'=>$users
        ]);
    }

    #[Route('user/addUser', name: 'addUser')]
    public function addUser(Request $request, EntityManagerInterface $em): Response
    {   
            $user = new User();
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                // Hash the new user's password
                $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($hashedPassword);
    
                // Set their role
                $user->setRoles(['ROLE_USER']);
    
                // Save
                $this->entityManager->persist($user);
                $this->entityManager->flush();
    
                return $this->redirectToRoute('display_users');
            }
    

            return $this->render('users/createUser.html.twig',[
            'form'=>$form->createView()]);
        
    }

    #[Route('user/removeUser/{id}', name: 'removeUser')]
    public function suppUser($id,EntityManagerInterface $entityManager): RedirectResponse
    {
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('sec_dashboard');
    }

    #[Route('user/updateUser/{id}', name: 'updateUser')]
    public function edit(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('No blog found for id '.$id);
        }

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            $entityManager->flush();

            return $this->redirectToRoute('display_users');
        }

        return $this->render('users/updateUser.html.twig',['form'=>$form->createView()]);
    }

}
