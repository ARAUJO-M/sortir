<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueEntity(fields={"pseudo"}, message="Ce pseudo est déjà utilisé.")
 * @UniqueEntity(fields={"mail"}, message="Cet email est déjà utilisé.")
 * @ORM\Entity(repositoryClass="App\Repository\ParticipantRepository")
 */
class Participant
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column (type="string", length=30)
     */
    private $nom;

    /**
     * @ORM\Column (type="string", length=30)
     */
    private $prenom;

    /**
     * @ORM\Column (type="string", length=15, nullable=true)
     */
    private $telephone;

    /**
     * @Assert\Length(
     *      min = 4,
     *      max = 25,
     *      minMessage = "Le pseudo doit être au minimum de {{ limit }} caractères.",
     *      maxMessage = "Le pseudo doit être au maximum de {{ limit }} caractères."
     * )
     * @Assert\Regex(pattern="/^[a-z0-9_-]+$/i", message="Le pseudo ne peut contenir que des caractères alphanumériques.")
     * @ORM\Column (type="string", length=30, unique=true)
     */
    private $pseudo;

    /**
     * @ORM\Column (type="string", length=20, unique=true)
     */
    private $mail;

    /**
     * @Assert\Length(
     *      min = 6,
     *      max = 20,
     *      minMessage = "Le mot de passe doit être au minimum de {{ limit }} caractères.",
     *      maxMessage = "Le mot de passe doit être au maximum de {{ limit }} caractères."
     * )
     * @ORM\Column (type="string", length=20)
     */
    private $motPasse;

    /**
     * @ORM\Column (type="boolean")
     */
    private $administrateur;

    /**
     * @ORM\Column (type="boolean")
     */
    private $actif;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Campus", inversedBy="participants", cascade={"persist"})
     */
    private $campus;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sortie", mappedBy="participantOrganisateur", cascade={"remove"})
     */
    private $sorties;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return mixed
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @param mixed $prenom
     */
    public function setPrenom($prenom): void
    {
        $this->prenom = $prenom;
    }

    /**
     * @return mixed
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * @param mixed $telephone
     */
    public function setTelephone($telephone): void
    {
        $this->telephone = $telephone;
    }

    /**
     * @return mixed
     */
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * @param mixed $pseudo
     */
    public function setPseudo($pseudo): void
    {
        $this->pseudo = $pseudo;
    }

    /**
     * @return mixed
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param mixed $mail
     */
    public function setMail($mail): void
    {
        $this->mail = $mail;
    }

    /**
     * @return mixed
     */
    public function getMotPasse()
    {
        return $this->motPasse;
    }

    /**
     * @param mixed $motPasse
     */
    public function setMotPasse($motPasse): void
    {
        $this->motPasse = $motPasse;
    }

    /**
     * @return mixed
     */
    public function getAdministrateur()
    {
        return $this->administrateur;
    }

    /**
     * @param mixed $administrateur
     */
    public function setAdministrateur($administrateur): void
    {
        $this->administrateur = $administrateur;
    }

    /**
     * @return mixed
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * @param mixed $actif
     */
    public function setActif($actif): void
    {
        $this->actif = $actif;
    }

    /**
     * @return mixed
     */
    public function getCampus()
    {
        return $this->campus;
    }

    /**
     * @param mixed $campus
     */
    public function setCampus($campus): void
    {
        $this->campus = $campus;
    }

    /**
     * @return mixed
     */
    public function getSorties()
    {
        return $this->sorties;
    }

    /**
     * @param mixed $sorties
     */
    public function setSorties($sorties): void
    {
        $this->sorties = $sorties;
    }



}
