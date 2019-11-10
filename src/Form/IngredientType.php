<?php

namespace App\Form;

use App\Entity\Ingredient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'placeholder' => "Nom de l'ingredient"
                ]
            ])
            ->add('unit', ChoiceType::class,['choices'=> [
                'Kilogramme'=> "Kilogramme",
                'Litre' => "Litre",
                'Piece' => "Piece",
                'Cuillère à soupe' => "Cuillère à soupe",
                'Cuillère à café' => "Cuillère à café"
                ]
            ])
            ->add('quantity', NumberType::class,[
                'attr' => [
                    'placeholder' => "quantité",
                    'step' => "any"
                ]
            ] )

        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ingredient::class,
        ]);
    }
}
