<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom complet',
                'attr' => ['class' => 'form-control', 'autocomplete' => 'name'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre nom.']),
                    new Length(['min' => 2, 'max' => 100]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['class' => 'form-control', 'autocomplete' => 'email'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre email.']),
                    new Email(['message' => "L'email n'est pas valide."]),
                ],
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'attr' => ['class' => 'form-control', 'autocomplete' => 'street-address'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre adresse.']),
                ],
            ])
            ->add('numTeleph', TelType::class, [
                'label' => 'Numéro de téléphone',
                'attr' => ['class' => 'form-control', 'autocomplete' => 'tel'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre numéro de téléphone.']),
                    new Length(['min' => 8, 'max' => 20]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => ['class' => 'form-control', 'autocomplete' => 'new-password'],
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'attr' => ['class' => 'form-control', 'autocomplete' => 'new-password'],
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un mot de passe.']),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                        'max' => 4096,
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
