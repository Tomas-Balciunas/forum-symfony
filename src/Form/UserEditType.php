<?php

namespace App\Form;

use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\UserSuspension;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'max' => 16,
                        'minMessage' => 'Username has to be between 3 and 16 characters.',
                        'maxMessage' => 'Username has to be between 3 and 16 characters.'
                    ]),
                ]
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('plainPassword', RepeatedType::class, ['type' => PasswordType::class,
                'mapped' => false,
                'required' => false,
                'invalid_message' => 'The password fields must match.',
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters long.',
                        'max' => 50,
                    ]),
                ],
                'first_options'  => ['label' => 'New Password'],
                'second_options' => ['label' => 'Repeat New Password'],
            ])
            ->add('currentPassword', PasswordType::class, [
                'label' => 'Current Password',
                'mapped' => false,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
