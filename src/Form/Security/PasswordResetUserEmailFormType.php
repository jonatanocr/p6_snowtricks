<?php

namespace App\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordResetUserEmailFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => ['placeholder' => 'Enter your email', 'class' => 'adminInput'],
                'label' => false,
            ])
            ->add('validForm', SubmitType::class, [
                'attr' => ['class' => 'btn btn-dark']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {

    }
}
