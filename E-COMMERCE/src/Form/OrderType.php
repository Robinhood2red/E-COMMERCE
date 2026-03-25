<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Order;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'ex: John']
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'ex: Wick']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email de contact',
                'required' => false, // Pas obligatoire de remplir
                'mapped' => !$options['data']->getEmail() ? true : false,
                'attr' => [
                    'placeholder' => 'ex: rambo@alpha.com',
                    //* aide visuelle
                    'class' => 'email-field' 
                ]
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'placeholder' => '06...',
                    'maxlength' => '10', // Bloque physiquement à 10 caractères
                    'pattern' => '[0-9]{10}', // HTML5 : impose exactement 10 chiffres
                    'oninput' => "this.value = this.value.replace(/[^0-9]/g, '');" // JS bloque tout ce qui n'est pas un n°
                    ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse de livraison',
                'attr' => ['placeholder' => '12 rue du muscle...']
            ])
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name',
                'label' => 'Zone Logistique',
                'placeholder' => 'Choisir ma ville (ou retrait magasin)',
                'required' => true, //* Comme ça pas obligatoire
                'attr' => ['class' => 'bg-dark text-white border-alpha']
            ])
            ->add('payOnDelivery', null, [
                'label'=>'Payez à la livraison',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}