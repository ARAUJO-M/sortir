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
    /**
     * @param AfficherSortiesData $data
     * @param UserInterface $getUser
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function trouverSortie(AfficherSortiesData $data, UserInterface $getUser): array
    {
        //instancier les formats date du form, place option par défaut date/heure du jour
        $dateTime = new \DateTime('now');

        //selection de la sortie et de son campus orga + état qui doit être en sortie créée
        $qb = $this->createQueryBuilder('s');
       // $qb->select('c', 's', 'e')
       //     ->join('s.campusOrganisateur', 'c')
       //     ->join('s.etatSortie', 'e')
       //     ->andWhere('e.libelle != (:libelle)') //todo: vérifier la pertinence
      //      ->setParameter('libelle', 'créée')
      //      ->andWhere('s.dateHeureDebut > (:date)')
      //      ->setParameter('date', $dateTime)
      //      ->orderBy('s.dateHeureDebut', 'DESC');

        //condition si le champ campus est indiqué
        if(!empty($data->campus)){
            $qb = $qb->addSelect('c')
                     ->join('s.campusOrganisateur', 'c')
                     ->andWhere('c.nom = (:campus)') //'c.id = (:campus)'
                     ->setParameter('campus', $data->campus);

        }
        //condition si le champ nom de la sortie remplie
        if(!empty($data->nom)){
            $qb = $qb->andWhere('s.nom LIKE :nom') //doc doctrine : ('cat.name LIKE :searchTerm')
                     ->setParameter('nom', "%{$data->nom}%"); //doc doctrine : ('searchTerm', '%'.$term.'%')
        }
        //conditions des différentes checkbox
        if($data->organisateur == true){
            $qb= $qb->andWhere('s.participantOrganisateur = (:organisateur)')
                    ->setParameter('organisateur', $getUser);
        }

        if($data->inscrit == true && $data->nonInscrit == true){

        } else {
            if($data->inscrit == true){
                $qb = $qb->addSelect('i') //alias inscrit
                         ->join('s.participants', 'i')
                         ->andWhere('i.username = (:participant)') //'i.id = (:participant)'
                         ->setParameter('participant', $getUser); //'participant', $getUser
            }

            if($data->nonInscrit == true){
               $qb = $qb->addSelect('ni') //alias non inscrit
                        ->leftJoin('s.participants', 'ni') //join
                        ->andWhere('ni.username is null OR ni.username != (:participant)') //'ni.id is null OR ni.id != (:participant)'
                        ->setParameter('participant', $getUser); //'participant', $getUser
            }
        }

        if($data->sortiePassee == true){
            $qb = $qb->addSelect('e')
                     ->leftJoin('s.etatSortie', 'e')
                     ->andWhere('e.libelle = (:etat)') //'e.libelle = (:libelle)'
                     ->setParameter('etat', 'clôturée'); //'libelle', 'cloturée'
        }

        if($data->dateHeureDebut !== null){
            $qb = $qb->andWhere('s.dateHeureDebut > = (:dateHeureDebut)')
                     ->setParameter('dateHeureDebut', $data->dateHeureDebut);
        }

        if($data->dateFin !== null){
            $qb = $qb->andWhere('s.dateHeureDebut < = (:dateFin)') //'s.dateFin >= (:dateFin)'
                     ->setParameter('dateFin', $data->dateFin);
        }

     //   $query = $qb->getQuery();
     //   $result = $query->getResult();
     //   return $result;
        return $qb->getQuery()->getResult();

    }

}
