<?php

namespace App\Form;

use App\Entity\Registration;
use App\Entity\Tournament;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tournament', EntityType::class, [
                'class' => Tournament::class,
                'choice_label' => 'tournamentName',
            ])
            ->add('player', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'En attente' => 'en attente',
                    'Confirmée' => 'confirmée',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Registration::class,
        ]);
    }
}
