<?php

namespace App\DataFixtures;

use App\Entity\Participant;
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
        $participant = new Participant();
        $participant->setNom('nom');
        $participant->setPrenom('prenom');
        $participant->setTelephone('0626020202');
        $participant->setMail('nom.prenom@gmail.com');
        $participant->setUsername('pseudo');
        $password = $this->encoder->encodePassword($participant, 'motdepasse');
        $participant->setPassword($password);
        $participant->setActif('1');
        $participant->setAdministrateur('1');
        $participant->setRoles(['ROLE_USER']);

        $manager->persist($participant);
        $manager->flush();
    }
}
