<?php

namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{
    // On injecte RequestStack pour gérer la session et le ProductRepository
    public function __construct(private readonly RequestStack $requestStack,private readonly ProductRepository $productRepository) {
        
    }

    public function getFullCart(): array
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        $cartData = [];
        $total = 0;

        foreach ($cart as $id => $quantity) {
            $product = $this->productRepository->find($id);
            if ($product) {
                $cartData[] = [
                    'product' => $product,
                    'qte' => $quantity
                ];
                $total += $product->getPrice() * $quantity;
            }
        }

        return [
            'cart' => $cartData,
            'total' => $total
        ];
    }
}