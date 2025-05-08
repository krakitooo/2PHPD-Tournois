<?php

namespace App\Form;

use App\Entity\SportMatch;
use App\Entity\Tournament;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminSportMatchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tournament', EntityType::class, [
                'class' => Tournament::class,
                'choice_label' => 'tournamentName',
            ])
            ->add('player1', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
            ])
            ->add('player2', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
            ])
            ->add('scorePlayer1', IntegerType::class, [
                'required' => false,
            ])
            ->add('scorePlayer2', IntegerType::class, [
                'required' => false,
            ])
            ->add('matchDate', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'en attente' => 'en attente',
                    'en cours' => 'en cours',
                    'terminé' => 'terminé',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SportMatch::class,
        ]);
    }
}

