<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function  __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        //Création d'un participant dans le BDD

        $participant = new Participant();
        $participant->setNom('gennaro');
        $participant->setPrenom('lucas');
        $participant->setTelephone('0626020202');
        $participant->setMail('gen.lu@gmail.com');
        $participant->setUsername('lucas');
        $password = $this->encoder->encodePassword($participant, 'motdepasse');
        $participant->setPassword($password);
        $participant->setActif('1');
        $participant->setAdministrateur('1');

        $manager->persist($participant);
        $manager->flush();

    }
}
