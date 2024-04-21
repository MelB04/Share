<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Fichier;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\ORM\EntityManagerInterface;

class PostFichierController extends AbstractController
{
    public function __invoke(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManagerInterface){
        $fichier = $request->files->get('file');
    
        if (!$fichier) {
            throw new BadRequestHttpException('No file uploaded');
        }
    
        $nomOriginal = $request->request->get('nomOriginal');
        $dateEnvoi = new \DateTime($request->request->get('dateEnvoi'));
        $extension = $request->request->get('extension');
        $taille = $request->request->get('taille');
        $proprietaireId = $request->request->get('proprietaire');
        $proprietaire = $userRepository->find($proprietaireId);

        if (!$proprietaire) {
            throw new BadRequestHttpException('No user found for id '.$proprietaireId);
        }

        $f = new Fichier();
        $f->setNomOriginal($nomOriginal);
        $f->setDateEnvoi($dateEnvoi);
        $f->setExtension($extension);
        $f->setTaille($taille);
        $f->setProprietaire($proprietaire);
        $f->setFile($fichier);
    
        $entityManagerInterface->persist($f);
        $entityManagerInterface->flush();

        return new Response('File uploaded successfully', Response::HTTP_CREATED);
    }
}


