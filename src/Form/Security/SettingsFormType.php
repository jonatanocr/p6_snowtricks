<?php


namespace App\Form\Security;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class SettingsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('avatar', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => ['image/jpeg', 'image/png',],
                        'mimeTypesMessage' => 'Please upload a valid image (.jpeg or .png) document',
                    ])
                ],
            ])
            ->add('username', TextType::class, [
                'attr' => ['placeholder' => 'Username', 'class' => 'adminInput'], 'label' => false,
            ])
            ->add('email', EmailType::class, [
                'attr' => ['placeholder' => 'Email', 'class' => 'adminInput'], 'label' => false,
            ])
            ->add('Save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-dark']
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}