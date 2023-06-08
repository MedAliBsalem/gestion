<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Rdv;
use Symfony\Component\HttpFoundation\RedirectResponse;




class DoctorController extends AbstractController
{
    #[Route('/doctor', name: 'app_doctor')]
    public function index(ManagerRegistry $registry): Response
    {
        $entityManager = $registry->getManager();
        $RdvRepository = $entityManager->getRepository(Rdv::class);
        $rdvs = $RdvRepository->findAll();
        return $this->render('doctor/index.html.twig', [
            'r'=>$rdvs
        ]);
    }
    #[Route('doctore/consulted/{id}', name: 'makeConsulted')]
    public function makeConsulted(int $id, EntityManagerInterface $entityManager): RedirectResponse
    {
        $rdv = $entityManager->getRepository(Rdv::class)->find($id);

        $rdv->setConsulted(true);
        $entityManager->flush();
        

        return $this->redirectToRoute('app_doctor');
    }
}
