<?php

namespace App\Form;

use App\Entity\Voiture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class VoitureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('matricule')
            ->add('marque')
            ->add('modele')
            ->add('annee')
            ->add('kilometrage')
            ->add('prixAchat')
            ->add('prixLocationJour')
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Disponible' => 'disponible',
                    'Loué' => 'loué',
                    'Vendu' => 'vendu',
                ],
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo (JPG/PNG/WEBP)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPG, PNG, WEBP).',
                    ])
                ],
            ])
            ->add('documents', null, [
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Voiture::class,
        ]);
    }
}
