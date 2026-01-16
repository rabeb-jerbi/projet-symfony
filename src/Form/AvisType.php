<?php

namespace App\Form;

use App\Entity\Avis;
use App\Entity\Client;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note', ChoiceType::class, [
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                ],
            ])
            ->add('commentaire')
            ->add('date', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                // ✅ mieux que id
                'choice_label' => fn(Client $c) => $c->getNom() . ' - ' . $c->getEmail(),
                'placeholder' => 'Choisir un client',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}
