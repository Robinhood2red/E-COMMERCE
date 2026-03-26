<?php

namespace App\Service;

use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripePayment
{
    private $redirectUrl;

    public function __construct()
    {
        Stripe::setApiKey($_SERVER['STRIPE_SECRET_KEY']);
        Stripe::setApiVersion('2024-06-20');
    }

    public function startPayment($cart, $shippingCost, $orderId)
    {

        $cartProducts = $cart['cart']; 

        $products = [
            [
                'qte' => 1,
                'price' => $shippingCost,
                'name' => "Frais de livraison"
            ]
        ];

        // On ajoute ensuite les produits du panier [cite: 4]
        foreach ($cartProducts as $value) {
            // Initialisation d'un tableau vide pour stocker les informations d'un produit
            $productItem = [];
            // Récupération du nom du produit
            $productItem['name'] = $value['product']->getName();
            // Récupération du prix du produit
            $productItem['price'] = $value['product']->getPrice();
            // Récupération de la quantité du produit
            $productItem['qte'] = $value['qte'];
            // Ajout du produit formaté au tableau des produits
            $products[] = $productItem;
        }
        //* On crée la session de paiement Stripe
        $session = Session::create([
            'line_items' => [ // Les produits qui vont etre envoyer
                ...array_map(fn(array $product) => [
                    'quantity' => $product['qte'],
                    'price_data' => [
                        'currency' => 'Eur',
                        'product_data' => [
                            'name' => $product['name'],
                        ],
                        'unit_amount' => (int)$product['price'] * 100, // Force l'entier pour Stripe
                    ],
                ], $products), // La virgule et la variable $products ferment le array_map
            ],
            'mode' => 'payment',
            'cancel_url' => 'http://localhost:8000/pay/cancel/' . $orderId,
            'success_url' => 'http://localhost:8000/pay/success/' . $orderId,
            'billing_address_collection' => 'required',
            'shipping_address_collection' => [
                'allowed_countries' => ['FR', 'EG'],
            ],
            'metadata' => [
                'order_id' => $orderId, //! Décomenter quand le panier sera lié
            ]
        ]);

        // l'URL de redirection générée par Stripe dans variable publique
        $this->redirectUrl = $session->url;
    }

    public function getStripeRedirectUrl()
    {
        return $this->redirectUrl;
    }
}