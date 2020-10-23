<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\GererProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/profile", name="user_profile")
     */
    public function profile(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager)
    {
        //Récupération du membre par son Id
        $user = $manager->getRepository(Participant::class)->find($this->getUser()->getId());

        //Récupération du mot de passe actuel du membre
        $currentPassword = $user->getPassword();

        //Création du formulaire
        $gererProfilForm = $this->createForm(GererProfilType::class, $user);


        //Récupération les données du formulaire
        $gererProfilForm->handleRequest($request);

        if($gererProfilForm->isSubmitted() && $gererProfilForm->isValid())
        {
            //Récupération mdp du formulaire
            $password = $gererProfilForm->get('password')->getData();

            //Mise à jour du mot de passe
            if(!empty($password)) {
                //Le mot de passe du formulaire
                $user->setPassword($encoder->encodePassword($user, $password));
            }else{
                //On garde l'ancien mdp
                    $user->setPassword($currentPassword);
            }

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Votre profil a bien été modifié !');
            return $this->redirectToRoute('sorties_accueil');
        }
        else{
            $this->addFlash('danger', 'Une erreur s\'est produite lors de la modification de votre profil');
        }

        return $this->render("user/profile.html.twig", [
            'gererProfilForm' => $gererProfilForm->createView()
        ]);
    }

}