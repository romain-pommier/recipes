<?php

namespace App\Form;
use function array_merge_recursive;
use Symfony\Component\Form\AbstractType;

class ApplicationType extends  AbstractType{
    /**
     * Permet d'avoir la configuration de base d'un champ
     *
     * @param string $label
     * @param string $placeholder
     * @param array $option
     * @return array
     */
    protected function getConfiguration($label, $placeholder, $option = [])
    {

        return array_merge_recursive([
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ]
        ],$option);
    }
}