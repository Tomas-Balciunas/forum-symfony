<?php

namespace App\Form;

use App\Entity\Board;
use App\Entity\DTO\TopicDTO;
use App\Entity\Topic;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class TopicType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 3, 'max' => 255, 'minMessage' => 'Title must be at least {{ limit }} characters long.', 'maxMessage' => 'Title cannot be longer than {{ limit }} characters.']),
                ]
            ])
            ->add('body', TextareaType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 3,
                        'max' => 5000,
                        'minMessage' => 'Body must be at least {{ limit }} characters long.',
                        'maxMessage' => 'Body cannot be longer than {{ limit }} characters long.',
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TopicDTO::class,
        ]);
    }
}
