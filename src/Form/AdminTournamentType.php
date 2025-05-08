<?php

namespace App\Form;

use App\Entity\Tournament;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminTournamentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tournamentName')
            ->add('startDate', DateType::class, ['widget' => 'single_text'])
            ->add('endDate', DateType::class, ['widget' => 'single_text'])
            ->add('location')
            ->add('description')
            ->add('maxParticipants')
            ->add('sport')
            ->add('organizer', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
            ])
            ->add('winner', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tournament::class,
        ]);
    }
}

