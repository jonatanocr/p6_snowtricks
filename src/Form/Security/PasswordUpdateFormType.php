<?php

namespace App\Form\Security;

use App\Entity\ChangePassword;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordUpdateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'attr' => ['placeholder' => 'Current password'],
                'label' => false,
            ])
            ->add('newPassword', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => [
                    'attr' =>['placeholder' => 'New Password'],
                    'label' => false,
                ],
                'second_options' => [
                    'attr' => ['placeholder' => 'Repeat Password'],
                    'label' => false,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a password',]),
                    new Length(['min' => 8, 'minMessage' => 'Your password should be at least {{ limit }} characters', 'max' => 4096,]),
                ],
            ])
            ->add('Save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ChangePassword::class,
        ]);
    }
}
