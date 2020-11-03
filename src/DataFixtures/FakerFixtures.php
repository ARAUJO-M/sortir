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

        $random = rand(0, count($campus)-1);

        //Création d'un utilisateur type avec données connues
        $dupont = new Participant();
        $dupont->setNom("Dupont");
        $dupont->setPrenom("Jean");
        $dupont->setUsername("jd59");
        $password = $this->encoder->encodePassword($dupont, "password");
        $dupont->setPassword($password);
        $dupont->setMail("jd@gmail.com");
        $dupont->setTelephone($faker->e164PhoneNumber);
        $dupont->setRoles(["ROLE_ADMIN"]);
        $dupont->setAdministrateur(false);
        $dupont->setActif(true);
        $dupont->setCampus($campus[0]);
        $manager->persist($dupont);
        $manager->flush();


        //Création des utilisateurs
        for($i=0; $i<15; $i++)
        {
            $user = new Participant();
            $user->setNom($faker->firstName);
            $user->setPrenom($faker->lastName);
            $user->setTelephone($faker->e164PhoneNumber);
            $user->setUsername($faker->userName);
            $user->setMail($faker->email);
            $password = $this->encoder->encodePassword($user, "password");
            $user->setPassword($password);
            $user->setRoles(["ROLE_USER"]);
            $user->setAdministrateur(false);
            $user->setActif(true);
            $user->setCampus($campus[$random]);

            $manager->persist($user);
            $manager->flush();
        }


        //Création des différents états des sorties
        $etat_name = ["Créée", "Ouverte", "Cloturée", "En cours", "Passée", "Annulée", "Archivée"];
        $etatSortie = [];
        foreach($etat_name as $value){
            $etat = new Etat();
            $etat->setLibelle($value);
            array_push($etatSortie, $etat);
            $manager->persist($etat);

        }


        //Création de sorties
        for($i=0; $i<15; $i++)
        {
            $sortie = new Sortie();
            $sortie->setParticipantOrganisateur($user);
            $sortie->setCampusOrganisateur($campus[$random]);
            $sortie->setEtatSortie($etatSortie[1]);
            $sortie->setNom($faker->word);
            $sortie->setDateHeureDebut($faker->dateTimeBetween($min = '-30 days', $max = '+30 days', $timeZone = null));
            $sortie->setDateLimiteInscription(date_add($sortie->getDateHeureDebut(), date_interval_create_from_date_string('-2 days')));
            $sortie->setNbInscriptionsMax($faker->numberBetween(1, 20));
            $sortie->setInfosSortie($faker->text(250));

            // lieu
            $lieu = new Lieu();
            $lieu->setNom($faker->name);
            $lieu->setRue($faker->streetName);
            // ville
            $ville = new Ville();
            $ville->setNom($faker->city);
            $ville->setCodePostal($faker->postcode);
            $manager->persist($ville);
            $lieu->setVille($ville);
            $manager->persist($lieu);
            // attribution d'un lieu à la sortie
            $sortie->setLieu($lieu);

            $manager->persist($sortie);
            $manager->flush();
        }
    }
}