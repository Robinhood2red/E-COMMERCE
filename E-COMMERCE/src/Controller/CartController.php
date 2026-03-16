<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    public function __construct(private readonly ProductRepository $productRepository) {}

    #[Route('/cart', name: 'app_cart', methods: ['GET'])]
    public function index(SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $panierEnrichi = [];
        $total = 0;

        foreach ($panier as $id => $quantite) {
            $product = $this->productRepository->find($id);
            if ($product) {
                $panierEnrichi[] = [
                    'produit' => $product,
                    'quantite' => $quantite
                ];
                $total += $product->getPrice() * $quantite;
            }
        }

        return $this->render('cart/index.html.twig', [
            'items' => $panierEnrichi,
            'total' => $total
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function add(int $id, SessionInterface $session): Response
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            $this->addFlash('danger', 'Unité introuvable.');
            return $this->redirectToRoute('app_home_page');
        }

        $panier = $session->get('panier', []);
        $quantiteActuelle = $panier[$id] ?? 0;

        //! Vérification par rapport au stock de l'entité Product
        if ($quantiteActuelle >= $product->getStock()) {
            $this->addFlash('warning', 'Alerte : Stock maximum atteint.');
        } else {
            $panier[$id] = $quantiteActuelle + 1;
            $session->set('panier', $panier);
            $this->addFlash('success', 'Produit ajouté au panier.');
        }

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_remove')]
    public function remove(int $id, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            if ($panier[$id] > 1) {
                $panier[$id]--;
            } else {
                unset($panier[$id]);
            }
            $this->addFlash('info', 'Panier mis à jour.');
        }

        $session->set('panier', $panier);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/delete/{id}', name: 'app_cart_delete')]
    public function delete(int $id, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            unset($panier[$id]);
            $this->addFlash('danger', 'Produit retirée de l\'inventaire.');
        }

        $session->set('panier', $panier);
        return $this->redirectToRoute('app_cart');
    }
}