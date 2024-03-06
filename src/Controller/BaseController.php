<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(): Response
    {
        return $this->render('base/index.html.twig', []);
    }

    #[Route('/apropos', name: 'app_apropos')]
    public function apropos(): Response
    {
        return $this->render('base/apropos.html.twig', []);
    }

    #[Route('/mentionslegales', name: 'app_mentionslegales')]
    public function mentionslegales(): Response
    {
        return $this->render('base/mentionslegales.html.twig', []);
    }

    #[Route('/api', name: 'app_api')]
    public function api()
    {
        return new JsonResponse([
            "data" => json_encode([
                "test" => "salut"
            ])
        ]);
    }

    #[Route('/api-connectuser', name: 'app_api-connectuser')]
    public function apiconnectuser(): Response
    {
        return $this->render('base/api.html.twig', []);
    }
}
