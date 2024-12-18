<?php

namespace App\Form;

use App\Entity\DTO\PostDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('body', TextareaType::class, [
                'attr' => [
                    'minlength' => 3,
                    'maxlength' => 5000,
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 3,
                        'max' => 5000,
                        'minMessage' => 'Post must be at least {{ limit }} characters long.',
                        'maxMessage' => 'Post cannot be longer than {{ limit }} characters long.',
                    ]),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PostDTO::class,
        ]);
    }
}
