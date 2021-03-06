<?php

namespace App\Form\Tricks;

use App\Entity\Category;
use App\Entity\Trick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class UpdateTrickFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['placeholder' => 'name'],
                'label' => false,
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['placeholder' => 'description'],
                'label' => false,
            ])
            ->add('category', EntityType::class, [
                'label' => false,
                'class' => Category::class,
                'choice_label' => 'name'
            ])
            ->add('mainPicture', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image (.jpeg or .png) document',
                    ])
                ],
            ])
            ->add('pictureFiles', FileType::class, [
                'mapped' => false,
                'label' => false,
                'required' => false,
                'multiple' => true,
                'attr'     => ['accept' => 'image/*', 'multiple' => 'multiple'],
                'constraints' => [new All([new File(['maxSize' => '1024k', 'mimeTypes' => ['image/jpeg', 'image/png',], 'mimeTypesMessage' => 'Please upload a valid image (.jpeg or .png) document',])])],
            ])
            ->add('Save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-dark mt-4']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
