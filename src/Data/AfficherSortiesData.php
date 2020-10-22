<?php


namespace App\Data;


use App\Entity\Campus;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

class AfficherSortiesData
{
    /**
     * @var Campus
     */
    public $campus;

    /**
     * @var string
     */
    public $nom;

    /**
     * @var DateTime|null
     * @Assert\GreaterThanOrEqual("-1 month")
     */
    public $dateHeureDebut;

    /**
     * @var Date|null
     */
    public $dateFin;

    /**
     * @var string
     */
    public $organisateur = false;

    /**
     * @var boolean
     */
    public $inscrit = false;

    /**
     * @var boolean
     */
    public $nonInscrit = false;

    /**
     * @var boolean
     */
    public $sortiePassee = false;


    /**
     * @var boolean
     */
    public $inactif = false;

}