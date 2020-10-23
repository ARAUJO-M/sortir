<?php

namespace App\Controller;

use App\Data\AfficherSortiesData;
use App\Entity\Sortie;
use App\Form\AfficherSortiesType;
use App\Form\CreerSortieType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GoOutController extends AbstractController
{
    /**
     * @Route("/accueil", name="sorties_accueil")
     */
    public function accueil (SortieRepository $repository, Request $request)
    {
        $data = new AfficherSortiesData();
        $formSortie =  $this->createForm(AfficherSortiesType::class, $data);
        $formSortie->handleRequest($request);
        $getUser = $this->getUser(); //récupère l'utilisateur en cours
        $sorties = $repository->trouverSortie($data, $this->getUser()); //appelle méthode de recherche du SortieRepository

        return $this->render("sortie/listeSorties.html.twig", [
                "sorties" => $sorties,
                "getUser" => $getUser,
                "formSortie" => $formSortie->createView()

    ]);

    }

 //   /**
 //    * @Route("/sorties/detail/{id}", name="sorties_detail", requirements={"id": "\d+"})
 //    */
 /*   public function detailSortie ($id, SortieRepository $repository)
    {
    //    $date = new \DateTime('now');
    //    $sortie = $repository->findOneBy(['id'=>$id]); //selectionne l'id de la sortie

        //todo: scénario si sorties passées : consultable mais plus d'inscription / date limite inscription aussi

        return $this->render('SortieDetail.html.twig', [
            'sortie' => $sortie
        ]);
    }
*/
    /**
     * @Route("/sortie/creer", name="sortie_creer")
     */
    public function creerSortie(Request $request, EntityManagerInterface $em, CampusRepository $campusRepository,
                                LieuRepository $lieuRepository, VilleRepository $villeRepository, EtatRepository $etatRepository)
    {
        $sortie = new Sortie();
        $sortie->setDateHeureDebut(new \DateTime('now'));
        $sortie->setDateLimiteInscription(new \DateTime('now'));

        $getUser = $this->getUser();

    //attitrer campus du user en cours
        $campus = $campusRepository->findOneBy(['id' => $this->getUser()->getCampus()]);

    //récupérer tous le listing de lieux et villes
        $lieux = $lieuRepository->findAll();
        $villes = $villeRepository->findAll();


    //création formulaire et récupération des données  / condition validation
        $creerSortieForm = $this->createForm(CreerSortieType::class, $sortie);
        $creerSortieForm->handleRequest($request);

        if($creerSortieForm->isSubmitted() && $creerSortieForm->isValid()){

            //vérification contraintes/pertinence liées aux date début et clôture
            if($sortie->getDateHeureDebut() >= $sortie->getDateLimiteInscription() &&
                $sortie->getDateHeureDebut() > new  \DateTime('now') &&
                 $sortie->getDateLimiteInscription() > new \DateTime('now')){

                //attribution des différents états à une sortie
                if(isset($_POST['enregistrer'])){
                    $etat = $etatRepository->findOneBy(['libelle' => 'créée']);
                } elseif (isset($_POST['publier'])){
                    $etat = $etatRepository->findOneBy(['libelle' => 'ouverte']);
                }

                $sortie->setCampusOrganisateur($campus);
                $sortie->setParticipantOrganisateur($getUser);
                $sortie->setEtatSortie($etat);

                $em->persist($sortie);
                $em->persist($etat);
                $em->flush();

                $this->addFlash('succes', 'Sortie ajoutée avec succès!');
                return $this->redirectToRoute('sorties_accueil');

                //todo: param les messages d'erreur si conditions non remplies

            }
        } elseif ($sortie == null){
            $this->addFlash('erreur', 'Une erreur est survenue lors de la soumission du formulaire');
        }

        return $this->render('sortie/creerSortie.html.twig', [
            'creerSortieForm' => $creerSortieForm->createView(),
            'lieux' => $lieux,
            'villes' => $villes
        ]);
    }
}