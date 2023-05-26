<?php

namespace App\Controller;


use App\Entity\Rdv;

use App\Form\RdvSecType;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SecretaireController extends AbstractController
{
    #[Route('/secretaire', name: 'sec_dashboard')]
    public function index(): Response
    {
        return $this->render('secretaire/secretaireDashboard.html.twig', [
            'controller_name' => 'SecretaireController',
        ]);
    }

}
