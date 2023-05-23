<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Rdv;
use App\Form\RdvType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Repository\RdvRepository;

class PatientController extends AbstractController
{

    private $userRepository;
    private $entityManager;

    public function __construct(RdvRepository $rdvRepository,UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    
    #[Route('/patient/{id}', name:'app_patient')]
    public function index(int $id, RdvRepository $rdvRepository): Response
    {
    $rdvs = $rdvRepository->findBy(['patient' => $id]);

    return $this->render('patient/listRdv.html.twig', [
        'id' => $id,
        'rd' => $rdvs,
    ]);
    }

    /**
     * @Route("/patient/addRdv/{id}", name="addRdv")
     */
    public function addRdv(Request $request, int $id): Response
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $rdv = new Rdv();
        $rdv->setPatient($user);
        $rdv->setValid(false);
        $form = $this->createForm(RdvType::class, $rdv);

        $form->handleRequest($request);

        $userId = $user->getId();

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($rdv);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_patient', ['id' => $userId]);
        }
        
        return $this->render('patient/addRdv.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
