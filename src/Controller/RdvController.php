<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Rdv;
use App\Form\RdvSecType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;



class RdvController extends AbstractController
{


    #[Route('/rdv', name: 'list_rdv')]
    public function listRdv(ManagerRegistry $registry):Response
    {
            $entityManager = $registry->getManager();
            $RdvRepository = $entityManager->getRepository(Rdv::class);
            $rdvs = $RdvRepository->findAll();
            return $this->render('rdv/ListeRdv.html.twig', [
                'r'=>$rdvs
            ]);
    }

    /**
    * @Route("/rdv/addrdv", name="add_rdv")
    */
    public function addRdv(Request $request,EntityManagerInterface $entityManager): Response
    {
    $rdv = new Rdv();
    $rdv->setValid(false);

    $form = $this->createForm(RdvSecType::class, $rdv);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($rdv);
        $entityManager->flush();

        // You can redirect to a different route or page after saving the appointment
        return $this->redirectToRoute('list_rdv');
    }

    return $this->render('rdv/AddRdv.html.twig', [
        'form' => $form->createView(),
    ]);
    }


    #[Route('rdv/delete/{id}', name: 'delete_rdv')]
    public function deleteRdv(int $id, EntityManagerInterface $entityManager): RedirectResponse
    {
        $rdv = $entityManager->getRepository(Rdv::class)->find($id);

        if (!$rdv) {
            throw $this->createNotFoundException('No Rdv found for id ' . $id);
        }

        $entityManager->remove($rdv);
        $entityManager->flush();

        return $this->redirectToRoute('sec_dashboard');

    }

    #[Route('rdv/updateRdv/{id}', name: 'updateRdv')]
    public function edit(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $rdv = $entityManager->getRepository(Rdv::class)->find($id);

        if (!$rdv) {
            throw $this->createNotFoundException('No blog found for id '.$id);
        }

        $form = $this->createForm(RdvSecType::class, $rdv);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('list_rdv');
        }

        return $this->render('rdv/update.html.twig',['f'=>$form->createView()]);
    }

    #[Route('rdv/makeValid/{id}', name: 'makeValid')]
    public function makeValid(int $id, EntityManagerInterface $entityManager): RedirectResponse
    {
        $rdv = $entityManager->getRepository(Rdv::class)->find($id);

        $rdv->setValid(true);
        $entityManager->flush();

        return $this->redirectToRoute('list_rdv');
    }






}




