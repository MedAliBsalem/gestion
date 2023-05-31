<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Rdv;
use App\Form\RdvSecType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SecretaireController extends AbstractController
{
    #[Route('/secretaire', name: 'sec_dashboard')]
    public function rdv(ManagerRegistry $registry): Response
    {
        $entityManager = $registry->getManager();
        $RdvRepository = $entityManager->getRepository(Rdv::class);
        $rdvs = $RdvRepository->findAll();
        $rdvCount = count($rdvs);

        $entityManager = $registry->getManager();
        $UserRepository = $entityManager->getRepository(User::class);
        $users = $UserRepository->findAll();
        $userCount = count($users)-2;



        return $this->render('secretaire/secretaireDashboard.html.twig', [
            'r'=>$rdvs,
            'u'=>$users,
            'rdvCount' => $rdvCount,
            'userCount' => $userCount
        ])
        ;
    }

    #[Route('secretaire/removeUser/{id}', name: 'removeUser')]
    public function suppUser($id, EntityManagerInterface $entityManager): RedirectResponse
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

    #[Route('/secretaire/listRdv', name: 'listRdv')]
    public function listrdv(ManagerRegistry $registry,Request $request,EntityManagerInterface $entityManager): Response
    {
        $entityManager = $registry->getManager();
        $RdvRepository = $entityManager->getRepository(Rdv::class);
        $rdvs = $RdvRepository->findAll();

        $rdv = new Rdv();
        $rdv->setValid(false);
    
        $form = $this->createForm(RdvSecType::class, $rdv);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rdv);
            $entityManager->flush();
    
            // You can redirect to a different route or page after saving the appointment
            return $this->redirectToRoute('sec_dashboard');
        }
        

        return $this->render('secretaire/listRdv.html.twig', [
            'r'=>$rdvs,
            'form' => $form->createView(),
        ])
        ;
    }

    #[Route('/secretaire/listPat', name: 'listPat')]
    public function listpat(ManagerRegistry $registry): Response
    {
        $entityManager = $registry->getManager();
        $UserRepository = $entityManager->getRepository(User::class);
        $users = $UserRepository->findAll();

        return $this->render('secretaire/listPat.html.twig', [
            'u'=>$users,
        ])
        ;
    }






}
