<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Form\AjoutFichierType;
use App\Entity\Fichier;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CategorieRepository;
use App\Repository\FichierRepository;
use App\Repository\UserRepository;
use App\Form\PartageWithType;
use Symfony\Component\Console\Helper\Dumper;

class FichierController extends AbstractController
{

    #[Route('/partager_fichier/{id}', name: 'app_partager_fichier_with')]
    public function partagerFichier(Request $request, FichierRepository $fichierRepository, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $fichier=null;
        if ($request->get('id') != null)
        {
            $id = $request->get('id');
            $fichier = $fichierRepository->find($id);
        }

        $form = $this->createForm(PartageWithType::class);
        //$idFichier = $request-> get('idfichier');

        if (!$fichier) {
            throw $this->createNotFoundException('Fichier non trouvé');
        }

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()){
                $idUser = $form->get('user')->getData();
                
                $user = $userRepository->find($idUser);

                if (!$user) {
                    throw $this->createNotFoundException('Utilisateur non trouvé');
                }
                                
                if ($fichier != null) {
                    $fichier -> addUser($user);
                    $em->persist($fichier); 
                    $em->flush();
                }
                
                $this->addFlash('notice','Partage ajouté'); 
                return $this->redirectToRoute('app_profil');  
            }
        } 

        return $this->render('fichier/partager_fichier.html.twig', [
            'form'=> $form->createView(),
            'fichier'=>$fichier
        ]);
    }

    #[Route('/delete-partage-fichier/{iduser}/{idfichier}', name: 'app_del_partagefichier')]
    public function delPartage(Request $request, UserRepository $userRepository, FichierRepository $fichierRepository, EntityManagerInterface $em): Response ##categorie est l'entité qu'il doit aller chercher en fonction de l'id. Il comprend tout :)
    {
        $idUser = $request-> get('iduser');
        $idFichier = $request-> get('idfichier');

        $user = $userRepository->find($idUser);
        $fichier = $fichierRepository->find($idFichier);

        $fichier -> removeUser($user);

        $em->persist($fichier); 
        $em->flush();
        $this->addFlash('notice','Partage supprimée'); 
        return $this->redirectToRoute('app_profil');
    }


    #[Route('/profil-telechargement-fichier/{id}', name: 'telechargement-fichier', requirements: ["id"=>"\d+"] )]
    public function telechargementFichier(int $id, EntityManagerInterface $entityManagerInterface) {
     
        $repoFichier = $entityManagerInterface->getRepository(Fichier::class); 
        $fichier = $repoFichier->find($id);
        if ($fichier == null){
            $this->redirectToRoute('app_profil-ajout-fichier'); 
        }
        else{
            return $this->file($this->getParameter('file_directory').'/'.$fichier->getNomServeur(), $fichier->getNomOriginal());
        } 
    } 


    #[Route('/profil-ajout-fichier', name: 'app_profil-ajout-fichier')]
    public function index(Request $request, SluggerInterface $slugger, CategorieRepository $categoriesRepository, EntityManagerInterface $entityManagerInterface): Response
    {
        $form = $this->createForm(AjoutFichierType::class);
        
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()){
                $fichier = $form->get('fichier')->getData();
                
                if($fichier){
                    $nomFichier = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                    $nomFichier = $slugger->slug($nomFichier);
                    $nomFichier = $nomFichier.'-'.uniqid().'.'.$fichier->guessExtension();
                    try{                 
                        $f = new Fichier();
                        $f->setNomServeur($nomFichier);
                        $f->setNomOriginal($fichier->getClientOriginalName());
                        $f->setDateEnvoi(new \Datetime());
                        $f->setExtension($fichier->guessExtension());
                        $f->setTaille($fichier->getSize());
                        $f->setProprietaire($this->getUser());
                       
                        $categories = $form->get('categorie')->getData();
                        foreach ($categories as $categorie){
                            $c = $categoriesRepository->find($categorie);
                            $f -> addCategory($c);                            
                        }

                        $fichier->move($this->getParameter('file_directory'), $nomFichier);
                        $this->addFlash('notice', 'Fichier envoyé');  
                    }
                    catch(FileException $e){
                        $this->addFlash('notice', 'Erreur d\'envoi');
                    }   
                                        
                    $entityManagerInterface->persist($f);
                    $entityManagerInterface->flush();   
                }
                return $this->redirectToRoute('app_profil-ajout-fichier');
            }
        } 

        return $this->render('fichier/ajout-fichier.html.twig', [
            'form'=> $form->createView()

        ]);
    }


}
