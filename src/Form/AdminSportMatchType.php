<?php

namespace App\Form;

use App\Entity\SportMatch;
use App\Entity\Tournament;
use App\Entity\User;
use App\Entity\Registration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminSportMatchType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tournament', EntityType::class, [
                'class' => Tournament::class,
                'choice_label' => 'tournamentName',
                'placeholder' => 'Sélectionner un tournoi',
                'required' => true,
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
            
        // Ajouter les champs player1 et player2 avec seulement les joueurs inscrits et confirmés
        $formModifier = function (FormInterface $form, Tournament $tournament = null) {
            if (null === $tournament) {
                // Si aucun tournoi n'est sélectionné, afficher une liste vide
                $form->add('player1', EntityType::class, [
                    'class' => User::class,
                    'choice_label' => 'username',
                    'placeholder' => 'Sélectionnez d\'abord un tournoi',
                    'choices' => [],
                    'disabled' => true,
                ]);
                
                $form->add('player2', EntityType::class, [
                    'class' => User::class,
                    'choice_label' => 'username',
                    'placeholder' => 'Sélectionnez d\'abord un tournoi',
                    'choices' => [],
                    'disabled' => true,
                ]);
            } else {
                // Récupérer les joueurs inscrits et confirmés au tournoi
                $confirmedPlayers = $this->entityManager->getRepository(Registration::class)
                    ->createQueryBuilder('r')
                    ->join('r.player', 'u')
                    ->where('r.tournament = :tournament')
                    ->andWhere('r.status = :status')
                    ->setParameter('tournament', $tournament)
                    ->setParameter('status', 'confirmée')
                    ->getQuery()
                    ->getResult();
                
                $players = array_map(function($registration) {
                    return $registration->getPlayer();
                }, $confirmedPlayers);
                
                $form->add('player1', EntityType::class, [
                    'class' => User::class,
                    'choice_label' => 'username',
                    'placeholder' => 'Choisir joueur 1',
                    'choices' => $players,
                ]);
                
                $form->add('player2', EntityType::class, [
                    'class' => User::class,
                    'choice_label' => 'username',
                    'placeholder' => 'Choisir joueur 2',
                    'choices' => $players,
                ]);
            }
        };
        
        // Ajouter un écouteur d'événement pour le champ tournament
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();
                $tournament = ($data && $data->getTournament()) ? $data->getTournament() : null;
                $formModifier($event->getForm(), $tournament);
            }
        );
        
        $builder->get('tournament')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $tournament = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $tournament);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SportMatch::class,
        ]);
    }
}