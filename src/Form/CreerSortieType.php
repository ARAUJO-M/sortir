<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreerSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie',
                'attr' => ['placeholder' => 'sortie théâtre'],
                'required' => true
            ])

            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie',
                'date_widget' => 'single_text',
                'required' => true
            ])

            ->add('dateLimiteInscription', DateType::class, [
                'label' => 'Date limite d\'inscription',
                'widget' => 'single_text',
                'required' => true
            ])

            ->add('nbInscriptionsMax', NumberType::class, [
                'label' => 'Nombre de places',
                'required' => true
            ])

            ->add('duree', IntegerType::class, [
                'label' => 'Durée (minutes)',
                'required' => false,
                'attr' => ['min' => 0]
            ])

            ->add('infosSortie', TextareaType::class, [
                'label' => 'Description et infos',
                'required' => false
            ])

            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'label' => 'Ville',
                'choice_label' => 'nom',
                'mapped' => false
            ])

            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'label' => 'Lieu',
                'choice_label' => 'nom',
                'required' => true,
                'mapped' => Ville::class

            ])

            ->add('latitude', NumberType::class, [
                'label' => 'Latitude',
                'input' => 'string',
                'required' => false,
                'mapped' => false
            ])

            ->add('longitude', NumberType::class, [
                'label' => 'Longitude',
                'input' => 'string',
                'required' => false,
                'mapped' => false
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class, Ville::class, Lieu::class
        ]);
    }
}
