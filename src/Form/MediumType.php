<?php

namespace App\Form;

use App\Entity\Medium;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class MediumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('path', HiddenType::class)
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'image' => 'picture',
                    'vidéo' => 'video',
                ]
            ])
            ->add('file', FileType::class, [
                'label' => 'Fichier',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        // 'maxSize' => '1024k',
                        // 'mimeTypes' => [
                        //     'application/pdf',
                        //     'application/x-pdf',
                        // ],
                        // 'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Medium::class,
        ]);
    }
}
