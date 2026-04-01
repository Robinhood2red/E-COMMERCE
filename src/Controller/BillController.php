<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
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

        //! ---------------------- ALTERNATIVE pour générer le pdf ---------------------------
        // // Récupération de la commande
        // $order = $orderRepository->find($id);

        // if (!$order) {
        //     throw $this->createNotFoundException("La commande demandée n'existe pas.");
        // }

        // // Définition des options PDF
        // $pdfOptions = new Options();
        // $pdfOptions->set('defaultFont', 'Arial'); // Définit la police par défaut

        // // Initialisation de Dompdf
        // $domPdf = new Dompdf($pdfOptions);

        // // Génération du HTML à partir du template Twig
        // $html = $this->renderView('bill/index.html.twig', [
        //     'order' => $order,
        // ]);

        // // Chargement du HTML dans Dompdf
        // $domPdf->loadHtml($html);

        // // Création du rendu PDF
        // $domPdf->render();

        // // Envoi du PDF au navigateur
        // $domPdf->stream('bill-' . $order->getId() . '.pdf', [
        //     'Attachment' => false // Permet d'afficher le PDF dans le navigateur au lieu de le télécharger
        // ]);

        // return new Response('', 200, [
        //     'Content-Type' => 'application/pdf'
        // ]);
        //! ---------------------- FIN ALTERNATIVE pour générer le pdf ---------------------------
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