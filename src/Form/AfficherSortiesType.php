<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AfficherSortiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus', EntityType::class, [
                'label' => 'Campus',
                'class' => Campus::class,
                'choice_label' => 'nom'
            ])
            ->add('nom', SearchType::class, ['label' => 'Nom de la sortie', 'attr' => ['placeholder' => 'Mot-clé de la sortie']])
            ->add('dateHeureDebut', DateType::class, ['label' => 'Entre'])
            ->add('dateFin', DateType::class, ['label' => 'Et'])
            ->add('organisateur', CheckboxType::class, ['label' => 'Sorties dont je suis l\'organisateur/trice', 'required' => false])
            ->add('inscrit', CheckboxType::class, ['label' => 'Sorties auxquelles je suis inscrit/e', 'required' => false])
            ->add('nonInscrit', CheckboxType::class, ['label' => 'Sorties auxquelles je ne suis pas inscrit/e', 'required' => false])
            ->add('sortiePassee', CheckboxType::class, ['label' => 'Sorties passées', 'required' => false])

            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AfficherSortiesType::class,
            'method' => 'get',
            'csrf_protection' => true
        ]);
    }
}
