<?php

namespace App\Form;

use App\Entity\DTO\SuspensionModifyDTO;
use App\Entity\UserSuspension;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SuspensionModifyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('expiresAt', DateTimeType::class, [
                'required' => false,
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
            ->add('isPermanent', CheckboxType::class, [
                'required' => false,
            ])
            ->add('modify', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SuspensionModifyDTO::class,
        ]);
    }
}