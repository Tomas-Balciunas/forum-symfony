<?php

namespace App\Form;

use App\Data\Roles;
use App\Entity\Board;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class BoardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'Title must be at least {{ limit }} characters long.',
                        'maxMessage' => 'Title must be at most {{ limit }} characters long.',
                    ]),
                ]
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 3,
                        'max' => 3000,
                        'minMessage' => 'Description must be at least {{ limit }} characters long.',
                        'maxMessage' => 'Description must be at most {{ limit }} characters long.',
                    ])
                ]
            ])
            ->add('access', ChoiceType::class, [
                'choices' => [
                    'Users' => Roles::ROLE_USER,
                    'Moderators' => Roles::ROLE_MODERATOR,
                    'Admins' => Roles::ROLE_ADMIN,
                ],
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Board::class,
        ]);
    }
}
