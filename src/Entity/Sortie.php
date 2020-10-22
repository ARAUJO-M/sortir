<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SortieRepository")
 */
class Sortie
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
     * @ORM\Column (type="datetime")
     */
    private $dateHeureDebut;

    /**
     * @ORM\Column (type="date", nullable=true)
     */
    private $dateFin;

    /**
     * @ORM\Column (type="integer", nullable=true)
     */
    private $duree;

    /**
     * @ORM\Column (type="datetime")
     */
    private $dateLimiteInscription;

    /**
     * @ORM\Column (type="integer")
     */
    private $nbInscriptionsMax;

    /**
     * @ORM\Column (type="text", length=500, nullable=true)
     */
    private $infosSortie;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Participant", inversedBy="inscriptionsSorties")
     */
    private $participants;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Participant", inversedBy="sorties",cascade={"persist"})
     */
    private $participantOrganisateur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Campus", inversedBy="sorties",cascade={"persist"})
     */
    private $campusOrganisateur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Lieu", inversedBy="sorties", cascade={"persist"})
     */
    private $lieu;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Etat", inversedBy="sorties", cascade={"persist"})
     */
    private $etatSortie;

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
    public function getDateHeureDebut()
    {
        return $this->dateHeureDebut;
    }

    /**
     * @param mixed $dateHeureDebut
     */
    public function setDateHeureDebut($dateHeureDebut): void
    {
        $this->dateHeureDebut = $dateHeureDebut;
    }

    /**
     * @return mixed
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * @param mixed $dateHFin
     */
    public function setDateFin($dateFin): void
    {
        $this->dateFin = $dateFin;
    }


    /**
     * @return mixed
     */
    public function getDuree()
    {
        return $this->duree;
    }

    /**
     * @param mixed $duree
     */
    public function setDuree($duree): void
    {
        $this->duree = $duree;
    }

    /**
     * @return mixed
     */
    public function getDateLimiteInscription()
    {
        return $this->dateLimiteInscription;
    }

    /**
     * @param mixed $dateLimiteInscription
     */
    public function setDateLimiteInscription($dateLimiteInscription): void
    {
        $this->dateLimiteInscription = $dateLimiteInscription;
    }

    /**
     * @return mixed
     */
    public function getNbInscriptionsMax()
    {
        return $this->nbInscriptionsMax;
    }

    /**
     * @param mixed $nbInscriptionsMax
     */
    public function setNbInscriptionsMax($nbInscriptionsMax): void
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;
    }

    /**
     * @return mixed
     */
    public function getInfosSortie()
    {
        return $this->infosSortie;
    }

    /**
     * @param mixed $infosSortie
     */
    public function setInfosSortie($infosSortie): void
    {
        $this->infosSortie = $infosSortie;
    }

    /**
     * @return mixed
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * @param mixed $participants
     */
    public function setParticipants($participants): void
    {
        $this->participants = $participants;
    }

    /**
     * @return mixed
     */
    public function getParticipantOrganisateur()
    {
        return $this->participantOrganisateur;
    }

    /**
     * @param mixed $participantOrganisateur
     */
    public function setParticipantOrganisateur($participantOrganisateur): void
    {
        $this->participantOrganisateur = $participantOrganisateur;
    }

    /**
     * @return mixed
     */
    public function getCampusOrganisateur()
    {
        return $this->campusOrganisateur;
    }

    /**
     * @param mixed $campusOrganisateur
     */
    public function setCampusOrganisateur($campusOrganisateur): void
    {
        $this->campusOrganisateur = $campusOrganisateur;
    }

    /**
     * @return mixed
     */
    public function getLieu()
    {
        return $this->lieu;
    }

    /**
     * @param mixed $lieu
     */
    public function setLieu($lieu): void
    {
        $this->lieu = $lieu;
    }

    /**
     * @return mixed
     */
    public function getEtatSortie()
    {
        return $this->etatSortie;
    }

    /**
     * @param mixed $etatSortie
     */
    public function setEtatSortie($etatSortie): void
    {
        $this->etatSortie = $etatSortie;
    }

}
