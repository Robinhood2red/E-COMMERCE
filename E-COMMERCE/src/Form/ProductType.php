<?php

namespace App\Form;

use App\Entity\AlphaCamp;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

// use Symfony\Component\Form\Extension\Constraints as Assert;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('product_description')
            ->add('price')
            ->add('stock')
            ->add('image', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false, // Ce n'est pas obligatoire
                'constraints' => [
                     new File(
                        maxSize: '1024k',
                        mimeTypes: [
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                        ],
                    maxSizeMessage: 'Votre image ne doit pas dépasser 1024ko',
                    mimeTypesMessage: 'Veuillez choisir un fichier de type image valide (jpeg, png, jpg) !',
                ),
            ],
        ])
            ->add('subCategory', EntityType::class, [
                'class' => AlphaCamp::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
