<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{ 
    //! le __construct sert à rendre le "magasin" (le ProductRepository) disponible partout dans le contrôleur 
    //! Sans le constructeur, la variable $productRepository n'existerait pas à l'intérieur de ces fonctions. Le constructeur l'attache à l'objet via $this
    //! Si tu n'avais pas ce __construct, tu serais obligé de demander le ProductRepository dans les arguments de chaque méthode individuellement, comme ceci :
        //! public function index(ProductRepository $repo, SessionInterface $session)
    //* Readonly = Indique que la propriété ne peut être modifiée qu'une seule fois (lors de l'initialisation).
    public function __construct(private readonly ProductRepository $productRepository)
    {

    }

    #[Route('/cart', name: 'app_cart', methods: ['GET'])]
    public function index(SessionInterface $session): Response
    {
        $cart = $session->get('cart', []); //* [] stock les id et quantitée
        $cartData = [];
        $total = 0; //! Prix
        $totalquantity = 0; //! Quantitée

        foreach ($cart as $id => $quantity) {
            $product = $this->productRepository->find($id); //* Récup l'id et toutes les info auxquelles elle est ratachée // $this->ON UTILISE LE CONSTRUCT
            if ($product) { 
                $cartData[] = [
                    'produit' => $product,
                    'quantite' => $quantity
                ];
                $total += $product->getPrice() * $quantity;
                $totalquantity += $quantity; // On additionne chaque quantité (diférent produits et similaires)
            }
        }

        return $this->render('cart/index.html.twig', [
            'items' => $cartData,
            'total' => $total,
            'totalQuantite' => $totalquantity 
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function add(int $id, SessionInterface $session): Response
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            $this->addFlash('danger', 'Produit introuvable.');
            return $this->redirectToRoute('app_home_page');
        }

        $cart = $session->get('cart', []);
        $actualQuantity = $cart[$id] ?? 0; //. ?? = L'opérateur de coalescence nulle //! Si $cart[id] ?EXISTE? Sinon 0

        //! Vérification par rapport au stock de l'entité Product
        if ($actualQuantity >= $product->getStock()) {
            $this->addFlash('warning', 'Alerte : Stock maximum atteint.');
        } else {
            $cart[$id] = $actualQuantity + 1;
            $session->set('cart', $cart);
            $this->addFlash('success', 'Produit ajouté au panier.');
        }

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_remove')]
    public function remove(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            if ($cart[$id] > 1) {
                $cart[$id]--;
            } else {
                unset($cart[$id]);
            }
            $this->addFlash('info', 'Panier mis à jour.');
        }

        $session->set('cart', $cart);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/delete/{id}', name: 'app_cart_delete')]
    public function delete(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            unset($cart[$id]);
            $this->addFlash('danger', 'Produit retirée du panier.');
        }

        $session->set('cart', $cart);
        return $this->redirectToRoute('app_cart');
    }
    
    #[Route('/cart/clear', name: 'app_cart_clear')] //! Suppression intégralité panier
    public function clear(SessionInterface $session): Response
    {
        $session->remove('cart');

        $this->addFlash('danger', 'Panier supprimé.');

        return $this->redirectToRoute('app_cart');
    }
}