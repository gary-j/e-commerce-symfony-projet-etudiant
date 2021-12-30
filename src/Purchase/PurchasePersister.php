<?php

namespace App\Purchase;

use App\Cart\CartService;
use DateTime;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchasePersister
{
    protected $security;
    protected $em;
    protected $cartService;

    public function __construct(Security $security, EntityManagerInterface $em, CartService $cartService)
    {

        $this->security = $security;
        $this->em = $em;
        $this->cartService = $cartService;
    }

    public function storePurchase(Purchase $purchase)
    {
        // 6. Nous allons la lier avec l'utilisateur actuellement connecté (Security)
        $purchase->setUser($this->security->getUser());
        // DateTime configurer dans l'entité en LifecycleCallback prePersist()
        // ->setPurchasedAt(new DateTime())

        // Total configuré dans l'entité Purchase en preFlush()
        // ->setTotal($this->cartService->getTotal());

        $this->em->persist($purchase);

        // dd($purchase);

        // 7. Nous lions la Purchase avec les produits dans le panier (Cartservice)

        foreach ($this->cartService->getDetailedCartItems() as $cartItem) {

            $purchaseItem = new PurchaseItem;

            $purchaseItem->setPurchase($purchase)
                ->setProduct($cartItem->product)
                ->setProductName($cartItem->product->getName())
                ->setQuantity($cartItem->qty)
                ->setProductPrice($cartItem->product->getPrice())
                ->setTotal($cartItem->getTotal());

            // $purchase->addPurchaseItem($purchaseItem);
            // inscription de la purchaseItem dans la purchase fait dans l'entité PurchaseItem directement en preFlush()

            $this->em->persist($purchaseItem);
        }

        // 8. Nous allons enregistrer la commande (EntityManagerInterface)
        $this->em->flush();
    }
}
