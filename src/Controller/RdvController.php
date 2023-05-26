<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Rdv;
use App\Form\RdvSecType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


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




}




