<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentSuccessController extends AbstractController
{

    /**
     * @Route("/purchase/terminate/{id}", name="purchase_payment_success" )
     * @IsGranted("ROLE_USER")
     */
    public function success($id, PurchaseRepository $purchaseRepository, EntityManagerInterface $em, CartService $cartService)
    {
        // 1. Je récupère la commande
        $purchase = $purchaseRepository->find($id);

        // VERIF USER DE LA COMMANDE SINON OU !USER ALORS REDIRECTION
        $purchaseUser = $purchase->getUser();

        if (
            !$purchase ||
            ($purchase && $purchaseUser !== $this->getUser()) ||
            ($purchase->getStatus() === Purchase::STATUS_PAID)
        ) {
            $this->addFlash('warning', "La commande n'existe pas.");
            return $this->redirectToRoute("purchase_index");
        }
        // 2. Je la fais passer au status PAID
        $purchase->setStatus(Purchase::STATUS_PAID);
        $em->flush($purchase);

        // 3. Je vide le panier
        $cartService->empty();

        // 4. Je redirige avec un Flash vers la liste des commandes
        $this->addFlash('succes', "La commande à bien été payée et confirmée");

        return $this->redirectToRoute('purchase_index', [], 302);
    }
}
