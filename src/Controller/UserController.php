<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;

class UserController extends AbstractController
{
    /*#[Route('/profil_user', name: 'app_profile_user')]
    public function profil2(): Response
    {
        return $this->render('user/profil2.html.twig', []);
    }*/

    #[Route('/private-liste-users', name: 'app_liste_users')]
    public function listeUsers(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('user/index.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/profile', name: 'app_profil')]
    public function profil(): Response
    {

        return $this->render('user/profil.html.twig', [

        ]);
    }
}
