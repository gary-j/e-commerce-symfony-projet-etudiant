<?php

namespace App\Controller\Purchase;

use App\Controller\Webhook\PaymentIntentController;
use Stripe\Stripe;
use App\Entity\Purchase;
use Stripe\PaymentIntent;
use App\Stripe\StripeService;
use App\Repository\PurchaseRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentController extends AbstractController
{

    /**
     * @Route("/purchase/pay/{id}", name="purchase_payment_form")
     * @IsGranted("ROLE_USER")
     */
    public function showPaymentForm($id, PurchaseRepository $purchaseRepository, StripeService $stripeService)
    {
        $purchase = $purchaseRepository->find($id);
        $purchaseUser = $purchase->getUser();


        if (
            !$purchase ||
            ($purchase && $purchaseUser !== $this->getUser()) ||
            ($purchase->getStatus() === Purchase::STATUS_PAID)
        ) {
            return $this->redirectToRoute('cart_show');
        };


        // dd($intent->client_secret);

        // je dois livrer au client la clé secret qui vient de $intent->client_secret
        $intent = $stripeService->getPaymentIntent($purchase);
        $stripePublicKey = $stripeService->getStripePublicKey();

        dump($stripePublicKey);


        return $this->render('purchase/payment.html.twig', [
            'clientSecret' => $intent->client_secret,
            'purchase' => $purchase,
            'stripePublicKey' => $stripePublicKey
        ]);
    }
}
