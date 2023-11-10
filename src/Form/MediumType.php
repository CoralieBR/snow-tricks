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
            ->add('type', ChoiceType::class, [
                'choices' => [
                    '...' => null,
                    'image' => 'picture',
                    'vidéo' => 'video',
                ],
                'attr' => [
                    'data-action' => 'change->picture#displayFormNextStep'
                ]
            ])
            ->add('path', null, [
                'row_attr' => [
                    'data-picture-target' => 'secondInput path',
                ],
                'required' => false,
            ])
            ->add('file', FileType::class, [
                'label' => 'Fichier',
                'row_attr' => [
                    'data-picture-target' => 'secondInput file',
                ],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
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
