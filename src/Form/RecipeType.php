<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Form\ImageType;
use App\Form\MealStyleTypeType;
use function array_merge;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;



class RecipeType extends ApplicationType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration("Titre:", "Tapez votre titre d'annonce"))
            //slug créer automatiquement -> entity
            ->add('coverImageFile', FileType::class, $this->getConfiguration("Image Principale de votre recette:", "Choisissez une image",['required' => false]))
            ->add('description', TextType::class, $this->getConfiguration("Description:", "Donnez une description"))
            ->add('content', TextareaType::class, $this->getConfiguration("Recette détaillée:", "Tapez les instructions de votre recette"))
            ->add('people', IntegerType::class, $this->getConfiguration("Nombre de personnes:", "Indiuez le nombre de personne"))
            ->add('cookingTime', IntegerType::class, $this->getConfiguration("Temps de cuisson:", "indiquer le temps de la cuisson"))
            ->add('mealstyles', CollectionType::class,['entry_type' => MealStyleType::class, 'allow_add' => true, 'allow_delete' => true])
            ->add('ingredients', CollectionType::class, ['entry_type' => IngredientType::class,'allow_add' => true, 'allow_delete' => true])
            ->add('recipePictures', CollectionType::class, ['entry_type' => ImageType::class,'allow_add' => true, 'allow_delete' => true]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}



