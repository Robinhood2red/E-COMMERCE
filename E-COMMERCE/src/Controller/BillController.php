<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BillController extends AbstractController
{
    #[Route('/editor/order/bill/{id}', name: 'app_bill')]
    #[IsGranted('ROLE_EDITOR')]
    public function index($id, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->find($id);

        if (!$order) {
            $this->addFlash('danger', "Le protocole de commande ID $id est introuvable.");
            return $this->redirectToRoute('app_order_message_show');
        }

        return $this->render('bill/index.html.twig', [
            'order' => $order,
        ]);
    }
}
    
    
    // #[Route('/editor/bill/{id}', name: 'app_order_bill')]
    // #[IsGranted('ROLE_EDITOR')]
    // public function index($id, OrderRepository $orderRepository): Response
    // {

    //     $order = $orderRepository->find($id);
    //     $this->entityManager->flush();

    //     return $this->render('order/order.html.twig', [
    //         'order' => $order,
    //     ]); 
    // }