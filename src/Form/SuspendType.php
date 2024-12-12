<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class SuspendType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('days', IntegerType::class, [
                'required' => false,
                'empty_data' => null,
                'constraints' =>
                    [
                        new Range([
                            'min' => 1,
                            'max' => 365,
                        ]),
                    ]
            ])
            ->add('hours', IntegerType::class, [
                'required' => false,
                'empty_data' => null,
                'constraints' =>
                    [
                        new Range([
                            'min' => 1,
                            'max' => 23,
                        ])
                    ]
            ])
            ->add('reason', TextareaType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 10,
                        'max' => 1000,
                    ])
                ]
            ])
        ;
    }
}