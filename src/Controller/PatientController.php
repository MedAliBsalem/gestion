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
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;



class PatientController extends AbstractController
{

    private $userRepository;
    private $entityManager;
    private $authorizationChecker;

    public function __construct(RdvRepository $rdvRepository,UserRepository $userRepository, EntityManagerInterface $entityManager,AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->authorizationChecker = $authorizationChecker;


        
    }

    
        #[Route('/patient/{id}', name:'app_patient')]
        public function index(int $id, RdvRepository $rdvRepository,UserRepository $userRepository): Response
        {
        $user = $userRepository->find($id);
        $rdvs = $rdvRepository->findBy(['patient' => $id]);

        return $this->render('patient/listRdv.html.twig', [
            'user' => $user,
            'id' => $id,
            'rd' => $rdvs,
        ]);
        }

    /**
     * @Route("/patient/addRdv/{id}", name="addRdv")
     */
    public function addRdv(Request $request, int $id,Security $security): Response
    {
        $user = $this->userRepository->find($id);

        $loggedInUser = $security->getUser();
        if (!$loggedInUser) {
            throw new AccessDeniedException('You are not authorized to access this resource.');
        }
        if ($loggedInUser->getId() !== $id) {
            throw new AccessDeniedException('You are not authorized to access this resource.');
        }

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
        
        if (!$this->authorizationChecker->isGranted('ROLE_USER') && $user->getId() !== $id) {
            throw new AccessDeniedException('You are not authorized to access this resource.');
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
            'idVariable' => $id
        ]);
    }
}
