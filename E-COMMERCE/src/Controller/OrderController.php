<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Order;
use App\Entity\OrderProducts;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\Cart;
use App\Service\StripePayment;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/order')]
class OrderController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $productRepository, 
        private readonly EntityManagerInterface $entityManager, 
        private readonly Cart $cartService, 
        private readonly MailerInterface $mailer
    ) {}
    
    #[Route(name: 'app_order')]
    public function index(Request $request, SessionInterface $session): Response 
    {
        $data = $this->cartService->getFullCart();
        $cartData = $data['cart'];
        $totalQuantity = 0;

        foreach ($cartData as $item) {
            $totalQuantity += $item['qte']; // On additionne les quantités de chaque produit
        }

        if (empty($cartData)) {
            return $this->redirectToRoute('app_cart');
        }

        $order = new Order();
        if ($this->getUser()) {
            $order->setEmail($this->getUser()->getUserIdentifier());
        }

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($data['total'])) {

                $totalPrice = $data['total'] + $order->getCity()->getShippingCost();
                
                $order->setTotalPrice($totalPrice);
                $order->setCreatedAt(new \DateTimeImmutable());
                $order->setIsPaymentCompleted(0); // Initialise à false (0)

                $this->entityManager->persist($order);
                $this->entityManager->flush(); // Flush initial pour générer l'ID de commande

                foreach ($cartData as $item) {
                    $orderProduct = new OrderProducts();
                    $orderProduct->setOrder($order); // Lie le produit à la commande
                    $orderProduct->setProduct($item['product']);
                    $orderProduct->setQte($item['qte']);

                    $this->entityManager->persist($orderProduct);
                }
                $this->entityManager->flush();

                if ($order->isPayOnDelivery()) {
                    // Mise à jour du panier en session (Vidage)
                    $session->set('cart', []);

                    $html = $this->renderView('mail/orderConfirm.html.twig', [
                        'order' => $order 
                    ]);

                    $email = (new Email())
                        ->from('sneakhub@gmail.com') // Comme dans la capture
                        ->to($order->getEmail())
                        ->subject('Confirmation de réception de commande')
                        ->html($html);

                    $this->mailer->send($email);
            
                    return $this->redirectToRoute('app_order_message', ['id' => $order->getId()]);
                } else {
                    $paymentStripe = new StripePayment();
                    
                    // On récupère les frais de port pour Stripe
                    $shippingCost = $order->getCity()->getShippingCost();
                    
                    // Ajout du coût total recalculé avec frais de port si nécessaire
                    $order->setTotalPrice($data['total'] + $shippingCost);
                    $this->entityManager->flush();

                    $paymentStripe->startPayment($data, $shippingCost, $order->getId());
                    $stripeRedirectUrl = $paymentStripe->getStripeRedirectUrl();

                    return $this->redirect($stripeRedirectUrl);
                }
            }
        }

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'total' => $data['total'], // Utilisé dans la capture
            'totalQte' => $totalQuantity,
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

    #[Route('/editor/show/{type?all}', name: 'app_order_message_show')]
    #[IsGranted('ROLE_EDITOR')]
    public function getAllOrder(string $type, OrderRepository $orderRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $filterDate = $request->query->get('filter_date');

        $queryBuilder = $orderRepository->createQueryBuilder('o')
            ->orderBy('o.id', 'DESC');

        // --- LOGIQUE DE FILTRAGE PAR TYPE
        if ($type === 'is-completed') {
            // Filtre les commandes marquées comme livrées/terminées
            $queryBuilder->andWhere('o.isCompleted = :completed')
                        ->setParameter('completed', 1);
        } elseif ($type === 'is-paid') {
            // Filtre les commandes payées via Stripe
            $queryBuilder->andWhere('o.isPaymentCompleted = :paid')
                        ->setParameter('paid', 1);
        } elseif ($type === 'no-delivery') { 
            // On cherche les commandes où isCompleted est soit NULL, soit 0
            $queryBuilder->andWhere('o.isCompleted IS NULL OR o.isCompleted = :notCompleted')
                        ->setParameter('notCompleted', 0);
        }
        // --- FILTRE PAR DATE EXISTANT ---
        if ($filterDate) {
            $date = new \DateTime($filterDate);
            
            $queryBuilder->andWhere('o.createdAt >= :start')
                        ->andWhere('o.createdAt <= :end')
                        ->setParameter('start', $date->format('Y-m-d 00:00:00'))
                        ->setParameter('end', $date->format('Y-m-d 23:59:59'));
        }

        $query = $queryBuilder->getQuery();

        $orders = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('order/order.html.twig', [
            'orders' => $orders,
            'currentDate' => $filterDate,
            'currentType' => $type // Utile pour garder l'onglet actif dans ton Twig
        ]); 
    }

    #[Route('/editor/is-completed/update/{id}', name: 'app_order_is-completed-update', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_EDITOR')]
    public function isColpletedUpdate(Request $request, $id, OrderRepository $orderRepository, EntityManagerInterface $entityManager): Response
    {

        $order = $orderRepository->find($id);
        $order->setIscompleted(true);
        $entityManager->flush();

        $this->addFlash('success', 'Modification effectuée !');

        // return $this->redirectToRoute('app_order_message_show');
        return $this->redirect($request->headers->get('referer'));
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