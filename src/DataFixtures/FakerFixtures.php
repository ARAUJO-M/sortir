<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FakerFixtures extends Fixture
{

    //Pour encoder les mots de passe des utilisateurs
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * FakerFixtures constructor.
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    // Création des utilisateurs + des sorties

    public function load(ObjectManager $manager)
    {

        //Initialisation de l'objet Faker
        $faker = Faker\Factory::create('fr_FR');

        //Création des différents campus
        $campus_name = array("Nantes", "Niort", "Rennes");
        $campus = [];
        foreach($campus_name as $value) {
            $camp = new Campus();
            $camp->setNom($value);
            array_push($campus, $camp);
            $manager->persist($camp);
            $manager->flush();
        }



        //Création des utilisateurs
       $user = [];
        for($i=0; $i<15; $i++)
        {
            $user[$i] = new Participant();
            $user[$i]->setNom($faker->firstName);
            $user[$i]->setPrenom($faker->lastName);
            $user[$i]->setTelephone($faker->e164PhoneNumber);
            $user[$i]->setUsername($faker->userName);
            $user[$i]->setMail($faker->email);
            $password = $this->encoder->encodePassword($user[$i], "password");
            $user[$i]->setPassword($password);
            $user[$i]->setAdministrateur(false);
            $user[$i]->setActif(true);

            $manager->persist($user[$i]);
            $manager->flush();
        }

        //Création des différents états des sorties

        $etat_name = ["Créée", "Ouverte", "Cloturée", "En cours", "Passées", "Annulée"];
        $etatSortie = [];
        foreach($etat_name as $value){
            $etat = new Etat();
            $etat->setLibelle($value);
            array_push($etatSortie, $etat);
            $manager->persist($etat);
            $manager->flush();
        }


        //Création des villes
        $ville = [];
        for($i=0; $i<5; $i++)
        {
            $ville[$i] = new Ville();
            $ville[$i]->setNom($faker->city);
            $ville[$i]->setCodePostal($faker->postcode);
            $manager->persist($ville[$i]);
            $manager->flush();
        }

        //Création de lieu
       $lieu = [];
        for($i=0; $i<10; $i++)
        {
            $lieu[$i] = new Lieu();
            $lieu[$i]->setNom($faker->name);
            $lieu[$i]->setRue($faker->streetName);
            $manager->persist($lieu[$i]);
            $manager->flush();
        }

        //Création de sorties
        $sortie = [];
        for($i=0; $i<15; $i++)
        {
            $sortie[$i] = new Sortie();
            $sortie[$i]->setNom($faker->word);
            $sortie[$i]->setDateHeureDebut($faker->dateTimeBetween($startDate = '-60 days', '+30 days'));
            $sortie[$i]->setDateLimiteInscription($faker->dateTimeBetween($startDate, '+14 days'));
            $sortie[$i]->setNbInscriptionsMax($faker->numberBetween(5, 20));
            $sortie[$i]->setInfosSortie($faker->text(250));
            $manager->persist($sortie[$i]);
            $manager->flush();
        }
    }
}