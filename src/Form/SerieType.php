<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Serie;
use App\Entity\Genre;
use App\Entity\Producteur;

use Symfony\Component\Form\Extension\Core\Type\FileType;


class SerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('nombreSaisons', null, [
                'label' => 'Nombre de saisons',
            ])
            
            ->add('synopsis')
            ->add('plateforme')
            ->add('dateDiffusion', null, [
                'widget' => 'single_text',
                'label' => 'Date de diffusion',
            ])
            ->add('note')
            ->add('genres', EntityType::class, [
                'class' => Genre::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Genre(s)',
            ])
            ->add('producteurNom', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Nom du ou des producteur(s)',
            ])
            ->add('image', FileType::class, [
                'label' => 'Image de la série (Optionnel)',
                'mapped' => false,
                'required' => false,
            ])
          ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Serie::class,
        ]);
    }
}