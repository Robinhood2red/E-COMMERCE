<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Order;
use App\Entity\OrderProducts;
use App\Form\OrderType;
use App\Repository\ProductRepository;
use App\Service\Cart;
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
    public function __construct(private readonly ProductRepository $productRepository, private readonly EntityManagerInterface $entityManager, private readonly Cart $cartService)
    {

    }
    
    #[Route(name: 'app_order')]
    public function index(Request $request): Response
    {
        // Récupération des données avec CartService
        $fullCart = $this->cartService->getFullCart();
        $cartData = $fullCart['cart'];
        $total = $fullCart['total'];

        // Calcul quantités
        $totalQuantity = 0;
        foreach ($cartData as $item) {
            $totalQuantity += $item['qte'];
        }

        // Si vide --> Panier
        if (empty($cartData)) {
            return $this->redirectToRoute('app_cart');
        }

        // Préparation entity
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $shipping = $order->getCity() ? $order->getCity()->getShippingCost() : 0; // Calcul selon la city
 
            $order->setTotalPrice($total + $shipping); //* TOTAL
            $order->setCreatedAt(new \DateTimeImmutable()); //* DATE

            $this->entityManager->persist($order);

            // Transformation de chaque élément du panier en entité de liaison OrderProducts
            foreach ($cartData as $item) {
                $orderProduct = new OrderProducts();
                $orderProduct->setOrder($order); //* Association avec la commande parente
                $orderProduct->setProduct($item['product']); //* Liaison vers l'entité Produit
                $orderProduct->setQte($item['qte']); //* Définition de la quantité achetée
                
                // Préparation de l'enregistrement sql
                $this->entityManager->persist($orderProduct);
            }

            // Envoie des requêtes sql
            $this->entityManager->flush();

            // Vide le panier
            $request->getSession()->remove('cart');

            $this->addFlash('success', 'Commande Alpha enregistrée.');
            return $this->redirectToRoute('app_home_page'); 
        }

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'total_items' => $total,
            'totalQuantite' => $totalQuantity,
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