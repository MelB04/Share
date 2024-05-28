<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\Fichier;
use App\Repository\UserRepository;
use App\Repository\FichierRepository;
use App\Repository\CategorieRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\ORM\EntityManagerInterface;
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
        $createurs = [
            [
                'id' => 1,
                'title' => 'Mélanie BOUDRY',
                'role' => 'Etudiante',
                'backgroundColor' => '#F05C5C',
                'url' => 'https://s4-8111.nuage-peda.fr/portfolio-mboudry/',
            ],
            [
                'id' => 2,
                'title' => 'Agathe POTEAUX',
                'role' => 'Etudiante',
                'backgroundColor' => '#87C48A',
                'url' => 'https://s4-8110.nuage-peda.fr/Portfolio/',
            ],
            [
                'id' => 3,
                'title' => 'Antoine BRUYE',
                'role' => 'Etudiant',
                'backgroundColor' => '#7A93EE',
                'url' => 'https://s4-8058.nuage-peda.fr/Portfolio/',
            ],
            [
                'id' => 4,
                'title' => 'Clément PARISOT',
                'role' => 'Etudiant',
                'backgroundColor' => '#EE8AFE',
                'url' => 'https://superkiment.fr/',
            ],
        ];
        return $this->render('base/apropos.html.twig', [
            'createurs'=>$createurs
        ]);
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

    
    #[Route('/api-updateuser', name: 'app_api-updateuser')]
    public function apiupdateuser(UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $response = [
            "data" => [],
            "state" => "fail",
            "message" => ""
        ];

        if (isset($_GET["id"])) {
            $id = htmlspecialchars($_GET["id"]);
            $utilisateur= $userRepository->findOneBy((['id' => $id]));
            if (isset($_GET["firstname"])) {
                $firstname = htmlspecialchars($_GET["firstname"]);
                $utilisateur->setFirstname($firstname);
            }
            if (isset($_GET["lastname"])) {
                $lastname = htmlspecialchars($_GET["lastname"]);
                $utilisateur->setLastname($lastname);
            }
            $em->persist($utilisateur);
            $em->flush();
        $response["data"] = $userRepository->findOneBy((['id' => $id]))->getJSON();
        $response["state"] = "success";
    } else {
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

    #[Route('/api-getfichiersPartageWithMe', name: 'app_api-getfichiersPartageWithMe')]
    public function getfichiersPartageWithMe(FichierRepository $fichierRepository): Response
    {
        $response = [
            "data" => [],
            "state" => "fail",
            "message" => ""
        ];

        if (isset($_GET["me"])) {
            $me = $_GET["me"];
            $fichiersPartages = $fichierRepository->findAll();

            $responseDataArray = [];
            if (!empty($fichiersPartages)) {
                foreach ($fichiersPartages as $fichier) {
                    $usersPartagees = $fichier->getUser();
                    foreach ($usersPartagees as $user) {
                        $idUserPartage = $user->getId();

                        if ($me == $idUserPartage){
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
                    }
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
            $responseCategorieArray = [];
            $responseUsersArray = [];
            if (!empty($fichier)) {
                $id = $fichier->getId();
                $proprietaire_name = $fichier->getProprietaireName();
                $nom_original = $fichier->getNomOriginal();
                $nom_serveur = $fichier->getNomServeur();
                $date_envoi = $fichier->getDateEnvoi();
                $extension = $fichier->getExtension();
                $categories = $fichier->getCategories();
                if (!empty($categories)) {
                    foreach ($categories as $categorie) {
                        $array_categorie = [
                            "id" => $categorie->getId(),
                            "libelle" => $categorie->getLibelle(),
                        ];
                        array_push($responseCategorieArray, $array_categorie); 
                    }
                }
                $usersPartagees = $fichier->getUser();
                if (!empty($usersPartagees)) {
                    foreach ($usersPartagees as $user) {
                        $array_user = [
                            "email" => $user->getEmail(),
                            "id" => $user->getId(),
                            "firstname" => $user->getFirstname(),
                            "lastname" => $user->getLastname(),
                        ];
                        array_push($responseUsersArray, $array_user); 
                    }
                }
                $taille = $fichier->getTaille();
                $array_fichier = [
                    "id" => $id,
                    "proprietaire_name" => $proprietaire_name,
                    "nom_original" => $nom_original,
                    "nom_serveur" => $nom_serveur,
                    "date_envoi" => $date_envoi,
                    "extension" => $extension,
                    "usersPartagees" => $responseUsersArray,
                    "categorie" => $responseCategorieArray,
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

    #[Route('/api-downloadfichier', name: 'app_api-downloadfichier')]
    public function downloadFile(Request $request) : Response
    {
        $fileName = $request->query->get('fileName');

        if (!file_exists("../uploads/fichiers/".$fileName)) {
            return new Response("Fichier introuvable", Response::HTTP_NOT_FOUND);
        }

        $response = new BinaryFileResponse($this->getParameter('file_directory').'/'.$fileName);
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            basename($fileName)
        ));

        return $response;
    }
}