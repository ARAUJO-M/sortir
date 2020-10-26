<?php

namespace App\Form;

use App\Data\AfficherSortiesData;
use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
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
            ->add('campus',EntityType::class, [
                'label' => 'Campus',
                'class' => Campus::class,
                'choice_label' => 'nom',
                'required' => false
            ])
            ->add('nom', SearchType::class, [
                'label' => 'Nom de la sortie',
                'required' => false,
                'attr' => ['placeholder' => 'Rechercher par nom de sortie']
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Entre',
                'required' => false,
                'date_widget' => 'single_text'

            ])
            ->add('dateFin', DateType::class, [
                'label' => 'Et',
                'required' => false,
                'widget' => 'single_text'
            ])

            ->add('organisateur', CheckboxType::class, ['label' => 'Sorties dont je suis l\'organisateur/trice', 'required' => false])
            ->add('inscrit', CheckboxType::class, ['label' => 'Sorties auxquelles je suis inscrit/e', 'required' => false])
            ->add('nonInscrit', CheckboxType::class, ['label' => 'Sorties auxquelles je ne suis pasinscrit/e', 'required' => false])
            ->add('sortiePassee', CheckboxType::class, ['label' => 'Sorties passées', 'required' => false])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AfficherSortiesData::class,
            'method' => 'get',
            'csrf_protection' => true
        ]);
    }
}
