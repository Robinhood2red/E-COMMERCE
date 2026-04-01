<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Service\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class StripeController extends AbstractController
{
    #[Route('/pay/success/{id}', name: 'app_pay_success')]
    public function success(Order $order, Cart $cartService, EntityManagerInterface $em, MailerInterface $mailer): Response
    {

        // Clear sans Cart.php / plus propre coté client
        $cartService->clear(); 

        //! On ne confirme la commande par mail QUE si le paiement est réussi.
        $this->sendConfirmationEmail($order, $mailer);

        $this->addFlash('success', 'Protocole d\'acquisition terminé. Paiement validé.');

        return $this->render('stripe/success.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/pay/cancel/{id}', name: 'app_pay_cancel')]
    public function cancel(Order $order): Response
    {
        $this->addFlash('danger', 'Paiement interrompu. La commande #' . $order->getId() . ' est en attente.');

        return $this->render('stripe/cancel.html.twig', [
            'order' => $order,
        ]);
    }

    //! ---------- Mail de Confirmation de paiement ------------
    private function sendConfirmationEmail(Order $order, MailerInterface $mailer) 
    { 
        $html = $this->renderView('mail/orderConfirm.html.twig', ['order' => $order]);
        $email = (new Email())
            ->from('noreply@alpha-system.com')
            ->to($order->getEmail())
            ->subject('Confirmation Alpha COMMANDO #' . $order->getId())
            ->html($html);
        $mailer->send($email);
    }

    #[Route('/stripe/notify', name: 'app_stripe_notify')]
    public function stripeNotify(Request $request, OrderRepository $or, EntityManagerInterface $em): Response
    {
        Stripe::setApiKey($_SERVER['STRIPE_SECRET_KEY']); // Clé API

        // Clé du Webhook
        $endpoint_secret = 'whsec_a18527d944ef877e75e3725eace1b0b1c50d1e527a8d87d814980b9ceb175bae';

        $payload = $request->getContent();
        $sigHeader = $request->headers->get('Stripe-Signature');
        $event = null;

        try {
            // Construction de l'événement et vérification de la signature
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            return new Response('Invalid payload', 400); //
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return new Response('Invalid signature', 400); //
        }

        //! ----- Traitement de l'événement ------
        switch ($event->type) {
            case 'payment_intent.succeeded': //* Événement de paiement réussi
                //* Récupére l'objet payment_intent
                $paymentIntent = $event->data->object;
                
                // Enregistrer les détails du paiement dans un fichier unique pour le debug
                $fileName = 'stripe-detail-' . uniqid() . '.txt';
                $orderId =$paymentIntent->metadata->orderId;
                $order = $or->find($orderId);

                $cartPrice = $order->getTotalPrice();
                $stripeTotalAmount = $paymentIntent->amount/100;
                if($cartPrice==$stripeTotalAmount) {
                    $order->setIsPaymentCompleted(1);
                    $em->flush();
                } 
                // file_put_contents($fileName, $orderId);
                break;

            case 'payment_method.attached': // Événement de méthode de paiement attachée
                // Récupérer l'objet payment_method
                $paymentMethod = $event->data->object;
                break;

            default:
                // Ne rien faire pour les autres types d'événements
                break;
        }

        // Retourner une réponse 200 pour indiquer que l'événement a été reçu avec succès
        return new Response('Événement reçu avec succès', 200); //
    }
}