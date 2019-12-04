<?php

namespace App\Form;

use App\Entity\MealStyle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MealStyleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type',ChoiceType::class,['choices'=> [
                'Entrée'=> "Entrée",
                'Plat' => "Plat",
                'Dessert' => "Dessert"
                ],

            ])
        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MealStyle::class,
        ]);
    }
}
