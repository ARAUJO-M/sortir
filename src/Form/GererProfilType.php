<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GererProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Pseudo ',
                'required' => false
            ])
            ->add('nom', TextType::class, [
                'required' => false
            ])
            ->add('prenom', TextType::class, [
                'required' => false
            ])
            ->add('telephone', NumberType::class, [
                'label' => 'Téléphone',
                'required' => false
            ])
            ->add('mail', EmailType::class, [
                'required' => false
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'les mots de passe ne correspondent pas entre eux',
                'first_options' => ['label' => 'Mot de passe '],
                'second_options' => ['label' => 'Confirmation '],
                'required' => false
            ])
            ->add('campus', EntityType::class, [
                'label' => 'Campus',
                'class' => Campus::class,
                'choice_label' => 'nom',
                'required' => false
            ])
            ->add('photo', FileType::class, [
                'required' => false
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
