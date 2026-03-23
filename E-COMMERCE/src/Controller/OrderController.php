<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/order')]
class OrderController extends AbstractController
{
    //* Utilisation du constructeur pour injecter le repository, comme dans CartController
    public function __construct(private readonly ProductRepository $productRepository, private readonly EntityManagerInterface $entityManager)
    {
        
    }
    
    #[Route(name: 'app_order')]
    public function index(Request $request, SessionInterface $session): Response
    {
        //! Récupération du panier brut (IDs et quantités)
        $cart = $session->get('cart', []);
        
        $cartData = [];
        $total = 0;
        $totalquantity = 0; //! Quantitée

        //! Transformation des données pour Twig comme dans CartController
        foreach ($cart as $id => $quantity) {
            $product = $this->productRepository->find($id);
            if ($product) {
                $cartData[] = [
                    'product' => $product,
                    'quantity' => $quantity
                ];
                // Calcul du prix total des articles
                $total += $product->getPrice() * $quantity;
                $totalquantity += $quantity;
            }
        }

        //* Formulaire Order
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        // 2. Gestion de la soumission
        if ($form->isSubmitted() && $form->isValid()) {
            
            // Récupération des frais de port via la ville choisie dans le formulaire
            $shipping = $order->getCity() ? $order->getCity()->getShippingCost() : 0;

            // Remplissage de l'entité Order avant envoi
            $order->setTotal($total + $shipping); // Vérifie que setTotal() existe dans Order.php
            $order->setCreatedAt(new \DateTimeImmutable());

            // ENVOI EN BDD
            $this->entityManager->persist($order);
            $this->entityManager->flush();

            // Nettoyage
            $session->remove('cart');
            $this->addFlash('success', 'Commande Alpha enregistrée.');

            return $this->redirectToRoute('app_home_page'); 
        }

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'total_items' => $total,
            'totalQuantite' => $totalquantity,
            'cart_data' => $cartData
        ]);
    
    }


    
    /*
     * Cette route est appelée par JavaScript (Fetch). 
     * Elle reçoit l'ID d'une ville et renvoie son prix au format JSON.*/
    // #[Route('/get-shipping-cost/{id}', name: 'app_shipping_cost', methods: ['GET'])]
    // public function getShippingCost(?City $city): JsonResponse
    // {
    //     // Si la ville n'existe pas (choix vide) = 0
    //     if (!$city) {
    //         return new JsonResponse(['cost' => 0]);
    //     }

    //     //! Renvoie un objet JSON : indispensable pour le js 
    //     return new JsonResponse([
    //         'cost' => $city->getShippingCost() // Utilise la propriété de l'entité City
    //     ]);
    // }
    #[Route('/get-shipping-cost/{id}', name: 'app_shipping_cost', methods: ['GET'])]
    public function getShippingCost(?City $city): Response
    {
        $cityShippingPrice = $city->getShippingCost();

       return new Response(json_encode(['status'=>200, "message"=>'on', 'content'=> $cityShippingPrice]));
    }
}