<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Repository\FichierRepository;

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

    //API

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
    public function apiconnectuser(UserRepository $userRepository): Response
    {
        $response = [
            "data" => [],
            "state" => "fail",
            "message" => ""
        ];

        if (isset($_GET["email"]) && isset($_GET["mdp"])) {
            $response["data"] = $userRepository->findAll();
            $response["state"] = "success";
        } else {
            $response["message"] = "Pas toutes les variables donnees";
        }

        return new JsonResponse($response);
    }

    #[Route('/api-getcheminfichier', name: 'app_api-getcheminfichier')]
    public function apigetcheminfichier(FichierRepository $fichierRepository): Response
    {
        $response = [
            "data" => [],
            "state" => "fail",
            "message" => ""
        ];

        if (isset($_GET["id"])) {
            $id = htmlspecialchars($_GET["id"]);
            $response["data"] = $fichierRepository->findOneBy((['id' => $id]))->getNomServeur();
            $response["state"] = "success";
        } else {
            $response["state"] = "fail";
            $response["message"] = "Pas toutes les variables donnees";
        }

        return new JsonResponse($response);
    }
}
