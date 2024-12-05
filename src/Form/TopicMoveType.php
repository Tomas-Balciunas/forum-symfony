<?php

namespace App\Form;

use App\Entity\Board;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class TopicMoveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('target', EntityType::class, [
                'class' => Board::class,
                'choice_label' => 'title',
                'choice_value' => 'id',
                'label' => 'Target board: ',
            ])
        ;
    }
}