<?php

namespace App\Form;

use App\Entity\Paiement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class ClientPaiementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('methode', ChoiceType::class, [
                'label' => 'Méthode de paiement',
                'placeholder' => 'Choisir une méthode',
                'choices' => [
                    'Carte bancaire' => 'CARTE',
                    'Virement' => 'VIREMENT',
                    'Espèces' => 'ESPECES',
                ],
                // ✅ style noir + id pour ton JS toggle
                'attr' => [
                    'class' => 'form-select bg-dark text-white border-secondary',
                    'id' => 'paiement_methode',
                ],
            ])

            ->add('nomPorteur', TextType::class, [
                'label' => 'Nom du porteur (si carte)',
                'required' => false,
                'trim' => true,
                'attr' => [
                    'placeholder' => 'Nom sur la carte',
                    'class' => 'form-control bg-transparent text-white border-secondary',
                    'autocomplete' => 'cc-name',
                ],
                'constraints' => [
                    // ✅ si l'utilisateur remplit, on valide la longueur
                    new Length([
                        'min' => 2,
                        'max' => 80,
                        'minMessage' => 'Le nom du porteur doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le nom du porteur ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])

            ->add('last4', TextType::class, [
                'label' => '4 derniers chiffres (si carte)',
                'required' => false,
                'trim' => true,
                'attr' => [
                    'maxlength' => 4,
                    'inputmode' => 'numeric',
                    'pattern' => '\d{4}',
                    'placeholder' => '1234',
                    'class' => 'form-control bg-transparent text-white border-secondary',
                    'autocomplete' => 'cc-number',
                ],
                'constraints' => [
                    // ✅ exactement 4
                    new Length([
                        'min' => 4,
                        'max' => 4,
                        'exactMessage' => 'Veuillez saisir exactement {{ limit }} caractères.',
                    ]),
                    // ✅ uniquement chiffres
                    new Regex([
                        'pattern' => '/^\d{4}$/',
                        'message' => 'Veuillez saisir exactement 4 chiffres.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Paiement::class,
        ]);
    }
}
