<?php

namespace App\Controller;

use App\Data\AfficherSortiesData;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\AfficherSortiesType;
use App\Form\AnnulerSortieType;
use App\Form\CreerSortieType;
use App\Form\DetailSortieType;
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
    public function accueil (EntityManagerInterface $em, Request $request)
    {
    // traitement de la recherche filtrée
        $sortieRepository = $em->getRepository(Sortie::class);
        $sorties = $sortieRepository->findAll();

        $data = new AfficherSortiesData();
        $formSortie = $this->createForm(AfficherSortiesType::class, $data);
        $formSortie->handleRequest($request);

        $getUser = $this->getUser();

        $sortiesFiltrees = $sortieRepository->trouverSortie($data, $getUser);
        $sorties = $sortiesFiltrees;

    // traitement des liens "Action"


        return $this->render('sortie/listeSorties.html.twig', [
            'sorties' => $sorties,
            'sortiesFiltrees' => $sortiesFiltrees,
            'formSortie' => $formSortie->createView()
        ]);


    }

    /**
     * @Route("/sorties/detail/{id}", name="sorties_detail", requirements={"id": "\d+"})
     */
    public function detailSortie ($id, SortieRepository $repository, EntityManagerInterface $em)
    {
        //Récupération de la sortie pour son id
       $sortie = $repository->findOneBy(['id'=>$id]); //selectionne l'id de la sortie

        //todo: scénario si sorties passées : consultable mais plus d'inscription / date limite inscription aussi

        return $this->render('sortie/SortieDetail.html.twig', [
            'sortie' => $sortie
        ]);
    }

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
        $etat = null;
                if(isset($_POST['enregistrer'])){
                    $etat = $etatRepository->findOneBy(['id' => '1']);
                } elseif (isset($_POST['publier'])){
                    $etat = $etatRepository->findOneBy(['id' => '2']);
                }

                $sortie->setCampusOrganisateur($campus);
                $sortie->setParticipantOrganisateur($getUser);
                $sortie->setEtatSortie($etat);

                $em->persist($sortie);
                $em->flush();

                $this->addFlash('success', 'Sortie ajoutée avec succès!');
                return $this->redirectToRoute('sorties_accueil');

            }
        } elseif ($sortie == null){
            $this->addFlash('error', 'Une erreur est survenue lors de la soumission du formulaire');
        }

        return $this->render('sortie/creerSortie.html.twig', [
            'creerSortieForm' => $creerSortieForm->createView(),
            'lieux' => $lieux,
            'villes' => $villes
        ]);
    }

    /**
     * @Route("/sortie/modifier/{id}", name="sortie_modifier", requirements={"id": "\d+"})
     */
    public function modifierSortie($id, EntityManagerInterface $em, Request $request,
                                   SortieRepository $sortieRepository, EtatRepository $etatRepository)
    {
        // Récupération de la sortie par son id
        $sortieRepository = $em->getRepository(Sortie::class);
        $sortie = $sortieRepository->find($id);

        // Création du formulaire
        $modifierSortieForm = $this->createForm(CreerSortieType::class, $sortie);
        $modifierSortieForm->handleRequest($request);

        if($modifierSortieForm->isSubmitted() && $modifierSortieForm->isValid()){

        //attribution des différents états à une sortie
            $etat = $sortie->getEtatSortie();

            if(isset($_POST['enregistrer'])){
                $etat = $etatRepository->findOneBy(['id' => '1']);
            } elseif (isset($_POST['publier'])){
                $etat = $etatRepository->findOneBy(['id' => '2']);
            }

            $sortie->setEtatSortie($etat);

            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'Les modifications de la sortie ont été prises en compte');
            return $this->redirectToRoute('sorties_accueil');
        } elseif ($modifierSortieForm->isSubmitted() && (!$modifierSortieForm->isValid())) {
            $this->addFlash('error', 'Une erreur s\'est produite lors de la modification de la sortie');
        }

        return $this->render("sortie/modifierSortie.html.twig", [
            'modifierSortieForm' => $modifierSortieForm->createView()
        ]);

    }

    /**
     * @Route("/sortie/annuler/{id}", name="sortie_annuler", requirements={"id": "\d+"})
     */
    public function annulerSortie($id, EntityManagerInterface $em, Request $request)
    {
            $sortieRepository = $em->getRepository(Sortie::class);
            $sortie = $sortieRepository->find($id);

            $etatRepository = $em->getRepository(Etat::class);

            $annulerSortieForm = $this->createForm(AnnulerSortieType::class, $sortie);
            $annulerSortieForm->handleRequest($request);

          if($annulerSortieForm->isSubmitted() && $annulerSortieForm->isValid()){
            $sortie->setEtatSortie($etat = $etatRepository->find('6')); //passage de l'état en Annulée (id 6 en bdd)

            $this->addFlash('success', 'L\'annulation de votre sortie a été prise en compte');

            $em->persist($sortie);
            $em->flush();
        } elseif ($sortie == null){
            $this->addFlash('erreur', 'Une erreur est survenue lors de la soumission du formulaire');
        }

        return $this->render('sortie/annulerSortie.html.twig', [
            'annulerSortieForm' => $annulerSortieForm->createView(),
            'sortie' => $sortie
        ]);
    }
}