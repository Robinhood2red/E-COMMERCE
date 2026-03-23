<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Order;
use App\Entity\OrderProducts;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
            
            // Vérification payOneDelivery est coché
            if ($order->isPayOnDelivery()) {

                // Calcul des frais de port selon la city choisie
                $shipping = $order->getCity() ? $order->getCity()->getShippingCost() : 0;

                $order->setTotalPrice($total + $shipping); // Commande + fraits de livraison
                $order->setCreatedAt(new \DateTimeImmutable()); // Ajout date de commande

                $this->entityManager->persist($order);

                foreach ($cartData as $item) {
                    $orderProduct = new OrderProducts();
                    $orderProduct->setOrder($order);
                    $orderProduct->setProduct($item['product']);
                    $orderProduct->setQte($item['qte']);

                    $this->entityManager->persist($orderProduct);
                }

                // Envoie en bdd
                $this->entityManager->flush();

                $request->getSession()->remove('cart');
                $this->addFlash('success', 'Commande Alpha enregistrée (Paiement à la livraison).');

                return $this->redirectToRoute('app_order_message', [
                    'id' => $order->getId()
                ]);
            } else {
                $this->addFlash('warning', 'Veuillez cocher Paiement à la livraison.');

                return $this->redirectToRoute('app_order');
            }
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

    #[Route('/order_message/{id}', name: 'app_order_message')]
    public function orderMesage(Order $order): Response
    {
       return $this->render('order/message.html.twig', [
        'order' => $order
       ]); 
       
    }

    #[Route('/editor/show', name: 'app_order_message_show')]
    #[IsGranted('ROLE_EDITOR')]
    public function getAllOrder(OrderRepository $orderRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // On récupère une requête (Query) et non plus directement les résultats
        $query = $orderRepository->createQueryBuilder('o')
            ->orderBy('o.id', 'DESC')
            ->getQuery();

        // Pagination comme pour homepage
        $orders = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), //* on commence à la page 1 / Grace à orderBy ce sera la dernière commande
            3 // nombre d'éléments par page
        );

        return $this->render('order/order.html.twig', [
            'orders' => $orders
        ]); 
    }
    #[Route('/editor/delete/{id}', name: 'app_order_delete', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_EDITOR')]
    public function deleteOrder(Order $order): Response
    {

        $this->entityManager->remove($order);
        $this->entityManager->flush();

        $this->addFlash('success', 'La commande a bien été supprimée !');

        return $this->redirectToRoute('app_order_message_show');
    }
}