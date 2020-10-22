<?php

namespace App\Repository;

use App\Data\AfficherSortiesData;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function trouverSortie(AfficherSortiesData $data, UserInterface $getUser)
    {
        //instancier les formats date du form, place option par défaut date/heure du jour
        $dateTime = new \DateTime('now');

        //selection de la sortie et de son campus orga + état qui doit être en sortie créée
        $query = $this->createQueryBuilder('s')
            ->select('c', 's', 'e')
            ->join('s.campusOrganisateur', 'c')
            ->join('s.etatSortie', 'e')
            ->andWhere('e.libelle != (:etat)') //todo: vérifier la pertinence
            ->setParameter('etat', 'créée')
            ->andWhere('s.dateHeureDebut > (:date)')
            ->setParameter('date', $dateTime)
            ->orderBy('s.dateHeureDebut', 'DESC');

        //condition si le champ campus est indiqué
        if(!empty($data->campus)){
            $query = $query
                ->andWhere('c.id = (:campus)')
                ->setParameter('campus', $data->campus);

        }
        //condition si le champ nom de la sortie remplie
        if(!empty($data->nom)){
            $query = $query
                ->andWhere('s.nom LIKE :nom') //doc doctrine : ('cat.name LIKE :searchTerm')
                ->setParameter('nom', "%{$data->nom}%"); //doc doctrine : ('searchTerm', '%'.$term.'%')
        }
        //conditions des différentes checkbox
        if(!empty($data->organisateur == true)){
            $query = $query
                ->andWhere('s.participantOrganisateur = (:organisateur)')
                ->setParameter('organisateur', $getUser);
        }

        if(!empty($data->inscrit == true)){
            $query = $query
                ->addSelect('i') //alias inscrit
                ->join('s.participants', 'i')
                ->andWhere('i.id =(:participant)')
                ->setParameter('participant', $getUser);
        }

        if(!empty($data->nonInscrit == true)){
            $query = $query
                ->addSelect('ni') //alias non inscrit
                ->join('s.participants', 'ni')
                ->andWhere('ni.id is null OR ni.id != (:participant)')
                ->setParameter('participant', $getUser);
        }

        if(!empty($data->sortiePassee == true)){
            $query = $query
                ->andWhere('e.libelle = (:libelle)')
                ->setParameter('libelle', 'cloturée');
        }

        if(!empty($data->dateHeureDebut !== null)){
            $query = $query
                ->andWhere('s.dateHeureDebut >= (:dateHeureDebut)')
                ->setParameter('dateHeureDebut', $data->dateHeureDebut);
        }

        if(!empty($data->dateFin !== null)){
            $query = $query
                ->andWhere('s.dateFin >= (:dateFin)')
                ->setParameter('dateFin', $data->dateFin);
        }

        return $query
            ->getQuery()
            ->getOneOrNullResult();


    }
}
