<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Repository\FichierRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Exception;

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
            $user = $userRepository->findOneBy(["email" => $_GET["email"]]);
            if ($user != null) {
                $response["data"] = $user->getJSON();
                $response["state"] = "success";
            } else
                $response["message"] = "Pas trouvé le user !";
        } else {
            $response["message"] = "Pas toutes les variables donnees";
        }

        return new JsonResponse($response);
    }

    #[Route('/api-getfichier', name: 'app_api-getfichier')]
    public function apigetfichier(FichierRepository $fichierRepository): Response
    {
        $response = [
            "data" => [],
            "state" => "fail",
            "message" => ""
        ];

        if (isset($_GET["id"])) {
            $id = $_GET["id"];
            $fichier = $fichierRepository->findOneBy((['id' => $id]));
            $proprietaire_id = $fichier->getProprietaireId();
            $nom_original = $fichier->getNomOriginal();
            $nom_serveur = $fichier->getNomServeur();
            $date_envoi = $fichier->getDateEnvoi();
            $extension = $fichier->getExtension();
            $taille = $fichier->getTaille();
            $array_fichier = [
                "id" => $id,
                "proprietaire_id" => $proprietaire_id,
                "nom_original" => $nom_original,
                "nom_serveur" => $nom_serveur,
                "date_envoi" => $date_envoi,
                "extension" => $extension,
                "taille" => $taille
            ];
            $response["data"] = $array_fichier;
            $response["state"] = "success";
        } else {
            $response["state"] = "fail";
            $response["message"] = "Pas toutes les variables donnees";
        }

        return new JsonResponse($response);
    }

    #[Route('/api-getfichiers', name: 'app_api-getfichiers')]
    public function apigetfichiers(FichierRepository $fichierRepository): Response
    {
        $response = [
            "data" => [],
            "state" => "fail",
            "message" => ""
        ];

        if (isset($_GET["proprietaire_id"])) {
            $proprietaire_id = $_GET["proprietaire_id"];
            $fichiers = $fichierRepository->findBy((['proprietaire' => $proprietaire_id]));
            $responseDataArray = [];
            if (!empty($fichiers)) {
                foreach ($fichiers as $fichier) {
                    $id = $fichier->getId();
                    $proprietaire_id = $fichier->getProprietaireId();
                    $nom_original = $fichier->getNomOriginal();
                    $nom_serveur = $fichier->getNomServeur();
                    $date_envoi = $fichier->getDateEnvoi();
                    $extension = $fichier->getExtension();
                    $taille = $fichier->getTaille();
                    $array_fichier = [
                        "id" => $id,
                        "proprietaire_id" => $proprietaire_id,
                        "nom_original" => $nom_original,
                        "nom_serveur" => $nom_serveur,
                        "date_envoi" => $date_envoi,
                        "extension" => $extension,
                        "taille" => $taille
                    ];
                    array_push($responseDataArray, $array_fichier);
                }
                $response["data"] = $responseDataArray;
                $response["state"] = "success";
                
            } else {
                $response["state"] = "fail";
                $response["message"] = "Aucun fichier";
            }
        } else {
            $response["state"] = "fail";
            $response["message"] = "Pas toutes les variables donnees";
        }
        return new JsonResponse($response);
    }


    #[Route('/api-getfichierByInfo', name: 'app_api-getfichierByInfo')]
    public function apigetfichierByInfo(FichierRepository $fichierRepository): Response
    {
        $response = [
            "data" => [],
            "state" => "fail",
            "message" => ""
        ];

        if (isset($_GET["idFichier"])) {
            $idFichier = $_GET["idFichier"];
            $fichier = $fichierRepository->findOneBy((['id' => $idFichier]));
            $responseDataArray = [];
            if (!empty($fichier)) {
                $id = $fichier->getId();
                $proprietaire_id = $fichier->getProprietaireId();
                $nom_original = $fichier->getNomOriginal();
                $nom_serveur = $fichier->getNomServeur();
                $date_envoi = $fichier->getDateEnvoi();
                $extension = $fichier->getExtension();
                $categorie = $fichier->getCategories();

                $usersPartagees = $fichier->getUser();
                $taille = $fichier->getTaille();
                $array_fichier = [
                    "id" => $id,
                    "proprietaire_id" => $proprietaire_id,
                    "nom_original" => $nom_original,
                    "nom_serveur" => $nom_serveur,
                    "date_envoi" => $date_envoi,
                    "extension" => $extension,
                    "categorie" => $categorie,
                    "usersPartagees" => $usersPartagees,
                    "taille" => $taille
                ];
                $response["data"] = $array_fichier;
                $response["state"] = "success";
                
            } else {
                $response["state"] = "fail";
                $response["message"] = "Aucun fichier";
            }
        } else {
            $response["state"] = "fail";
            $response["message"] = "Pas toutes les variables donnees";
        }
        return new JsonResponse($response);
    }
}
