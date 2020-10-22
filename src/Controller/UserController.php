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

        $user = $this->getUser();

        $gererProfilForm = $this->createForm(GererProfilType::class, $user);

        $gererProfilForm->handleRequest($request);

        if($gererProfilForm->isSubmitted() && $gererProfilForm->isValid())
        {

            $user = $gererProfilForm->getData();

            $hashed = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hashed);

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