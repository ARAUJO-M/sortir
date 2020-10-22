<?php

namespace App\Controller;

use App\Data\AfficherSortiesData;
use App\Entity\Sortie;
use App\Form\AfficherSortiesType;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GoOutController extends AbstractController
{
    /**
     * @Route("/accueil", name="sorties_accueil", requirements={"id": "\d+"})
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
        dump($getUser.$sorties);
    }

 //   /**
 //    * @Route("/sorties/{id}", name="sorties_detail")
 //    */
/*    public function detailSortie ($id, SortieRepository $repository)
    {
        $date = new \DateTime('now');
        $sortie = $repository->findOneBy(['id'=>$id]); //selectionne l'id de la sortie

        //todo: scénario si sorties passées : consultable mais plus d'inscription / date limite inscription aussi

        return $this->render('SortieDetail.html.twig', [
            'sortie' => $sortie
        ]);
    }
*/
}