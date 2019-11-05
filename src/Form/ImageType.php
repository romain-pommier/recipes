<?php

namespace App\Form;

use App\Entity\RecipePicture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('recipePictureFile', FileType::class, [
                'attr' => [
                    'placeholder' => "Choisissez une image",

                ],'required' => false
            ])
            ->add('caption', TextType::class, [
                'attr' => [
                    'placeholder' => "Titre de l'image"
                ]
            ])
            ->add('recipePictureName', TextType::class, [
                'attr' => [
                    'placeholder' => "Titre de l'image"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RecipePicture::class,
        ]);
    }
}
