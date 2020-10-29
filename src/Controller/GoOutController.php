<?php

namespace App\Controller;

use App\Data\AfficherSortiesData;
use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\AfficherSortiesType;
use App\Form\AnnulerSortieType;
use App\Form\CreerSortieType;
use App\Form\ModifierSortieType;
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
    public function accueil (EntityManagerInterface $em, Request $request, EtatRepository $etatRepository)
    {
    // traitement de la recherche filtrée
        $sortieRepository = $em->getRepository(Sortie::class);
        $sorties = $sortieRepository->findAll();

        $data = new AfficherSortiesData();
        $formSortie = $this->createForm(AfficherSortiesType::class, $data);
        $formSortie->handleRequest($request);

        $getUser = $this->getUser();

        $sortiesFiltrees = $sortieRepository->trouverSortie($data, $getUser);


    // gestion des états
        foreach ($sorties as $sortie){

            $dateJour = new \DateTime('now');
            $dateLimiteSortie = $sortie->getDateLimiteInscription();
            $dateSortie = $sortie->getDateHeureDebut();
            $participants = $sortie->getParticipants();
            $inscriptionsMax = $sortie->getNbInscriptionsMax();

            if($sortie->getEtatSortie() != '1'){

            //clôturer
            if($sortie->getEtatSortie() == '2' and ($dateLimiteSortie < $dateJour or count($participants) == $inscriptionsMax)){
                 $etat = $etatRepository->findOneBy(['id' => '3']);
                 $sortie->setEtatSortie($etat);
                 $em->persist($sortie);
                }
            //en cours
            elseif ($dateSortie == $dateJour){
                $etat = $etatRepository->findOneBy(['id' => '4']);
                $sortie->setEtatSortie($etat);
                $em->persist($sortie);
            }
            //passée
            elseif ($dateSortie < $dateJour){
                $etat = $etatRepository->findOneBy(['id' => '5']);
                $sortie->setEtatSortie($etat);
                $em->persist($sortie);
            }
            }
            $em->flush();
            $sorties = $sortiesFiltrees;
        }


        return $this->render('sortie/listeSorties.html.twig', [
            'sorties' => $sorties,
            'sortiesFiltrees' => $sortiesFiltrees,
            'formSortie' => $formSortie->createView()
        ]);


    }

    /**
     * @Route("/sorties/detail/{id}", name="sorties_detail", requirements={"id": "\d+"})
     */
    public function detailSortie ($id, SortieRepository $repository)
    {
        //Récupération de la sortie pour son id
       $sortie = $repository->findOneBy(['id'=>$id]); //selectionne l'id de la sortie


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

        if ($creerSortieForm->isSubmitted() && $creerSortieForm->isValid()) {

            //vérification contraintes/pertinence liées aux date début et clôture
            if ($sortie->getDateHeureDebut() >= $sortie->getDateLimiteInscription() &&
                $sortie->getDateHeureDebut() > new  \DateTime('now') &&
                $sortie->getDateLimiteInscription() > new \DateTime('now')) {

                //attribution des différents états à une sortie
                $etat = null;
                if (isset($_POST['enregistrer'])) {
                    $etat = $etatRepository->findOneBy(['id' => '1']);
                } elseif (isset($_POST['publier'])) {
                    $etat = $etatRepository->findOneBy(['id' => '2']);
                }

                $sortie->setCampusOrganisateur($campus);
                $sortie->setParticipantOrganisateur($getUser);
                $sortie->setEtatSortie($etat);

                $em->persist($sortie);
                $em->flush();

                $this->addFlash('success', 'Sortie ajoutée avec succès!');
                return $this->redirectToRoute('sorties_accueil');

            } else {
                $this->addFlash('error', 'Une erreur est survenue lors de la soumission du formulaire. Vérifiez vos dates');
            }
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
    public function modifierSortie($id, EntityManagerInterface $em, Request $request, EtatRepository $etatRepository)
    {
        // Récupération de la sortie par son id
        $sortieRepository = $em->getRepository(Sortie::class);
        $sortie = $sortieRepository->find($id);

        // Création du formulaire
        $modifierSortieForm = $this->createForm(ModifierSortieType::class, $sortie);
        $modifierSortieForm->handleRequest($request);

        if($sortie->getEtatSortie() == '1') {
            if ($modifierSortieForm->isSubmitted() && $modifierSortieForm->isValid()) {

                // si enregistrer ou publier
                if (isset($_POST['enregistrer'])) {
                    $etat = $etatRepository->findOneBy(['id' => '1']);
                    $sortie->setEtatSortie($etat);
                    $em->persist($sortie);
                    $em->flush();
                    $this->addFlash('success', 'Sortie enregistrée');
                } elseif (isset($_POST['publier'])) {
                    $etat = $etatRepository->findOneBy(['id' => '2']);
                    $sortie->setEtatSortie($etat);
                    $em->persist($sortie);
                    $em->flush();
                    $this->addFlash('success', 'Sortie publiée');
                }

                // si supprimer
                if (isset($_POST['supprimer'])) {
                    $em->remove($sortie);
                    $em->flush();
                    $this->addFlash('success', 'Votre sortie a été supprimée');
                }

                return $this->redirectToRoute('sorties_accueil');

            } elseif ($modifierSortieForm->isSubmitted() && (!$modifierSortieForm->isValid())) {
                $this->addFlash('error', 'Une erreur s\'est produite. Modifications non prise en compte');
            }
        }else{
            $this->addFlash('error', 'La sortie est déjà publiée et ne peut plus être modifiée');
            return $this->redirectToRoute('sorties_accueil');
        }
        return $this->render("sortie/modifierSortie.html.twig", [
            'modifierSortieForm' => $modifierSortieForm->createView(),
            'sortie' => $sortie
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

            $dateJour = new \DateTime('now');
            $dateSortie = $sortie->getDateHeureDebut();

            $annulerSortieForm = $this->createForm(AnnulerSortieType::class, $sortie);
            $annulerSortieForm->handleRequest($request);

          if($dateJour < $dateSortie) {
              if ($annulerSortieForm->isSubmitted() && $annulerSortieForm->isValid()) {
                  $sortie->setEtatSortie($etat = $etatRepository->find('6')); //passage de l'état en Annulée (id 6 en bdd)

                  $this->addFlash('success', 'L\'annulation de votre sortie a été prise en compte');

                  $em->persist($sortie);
                  $em->flush();
              } elseif ($sortie == null) {
                  $this->addFlash('erreur', 'Une erreur est survenue lors de la soumission du formulaire');
              }
          }else{
              $this->addFlash('error', 'La sortie est en cours et ne peut plus être annulée');
          }

        return $this->render('sortie/annulerSortie.html.twig', [
            'annulerSortieForm' => $annulerSortieForm->createView(),
            'sortie' => $sortie
        ]);
    }

    /**
     * @Route ("/inscrire/{id}", name="inscrire", requirements={"id": "\d+"})
     */
    public function inscrire($id, EntityManagerInterface $em)
    {
        $sortieRepository = $em->getRepository(Sortie::class);
        $sortie = $sortieRepository->find($id);
        $participant = $this->getUser();

        //Vérifier si places disponibles et date limite ok
        $limiteInscription = $sortie->getDateLimiteInscription();
        $dateJour = date('now');
        $inscrits = $sortie->getParticipants();
        $inscriptionsMax = $sortie->getNbInscriptionsMax();

        if(($dateJour < $limiteInscription) && (count($inscrits) < $inscriptionsMax)){

            $sortie->ajouterParticipant($participant); //méthode dans l'Entity Sortie

            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'Inscription à la sortie réussie' );

            return $this->redirectToRoute('sorties_accueil');

        } elseif ($dateJour > $limiteInscription) {

            $this->addFlash('error', 'Trop tard... les inscriptions sont closes pour cette sortie');

        } elseif (count($inscrits) > $inscriptionsMax) {

            $this->addFlash('error', 'Il n\'y a plus de places pour cette sortie');
        }

        return $this->redirectToRoute('sorties_accueil');
    }


    /**
     * @Route ("/desister/{id}", name="desister", requirements={"id": "\d+"})
     */
    public function desister($id, EntityManagerInterface $em)
    {
        $sortieRepository = $em->getRepository(Sortie::class);
        $sortie = $sortieRepository->find($id);
        $participant = $this->getUser();
        $dateJour = new \DateTime('now');
        $limiteInscription = $sortie->getDateLimiteInscription();

        if($dateJour < $limiteInscription){
           $sortie->retirerParticipant($participant);

            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'Vous avez été désinscrit de la sortie');
        } else {
            $this->addFlash('error', 'Se désister n\'est plus possible. Les inscriptions sont closes');
        }

        return $this->redirectToRoute('sorties_accueil');
    }

}