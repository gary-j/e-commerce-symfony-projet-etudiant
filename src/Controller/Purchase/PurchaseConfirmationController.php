<?php

namespace App\Controller\Purchase;

use DateTime;
use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManager;
use App\Form\CartConfirmationType;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class PurchaseConfirmationController extends AbstractController
{
    protected $cartService;
    protected $em;

    public function __construct(CartService $cartService, EntityManagerInterface $em)
    {
        $this->cartService = $cartService;
        $this->em = $em;
    }

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * @IsGranted("ROLE_USER", message="Merci de vous connecter pour confirmer une commande")
     */
    public function confirm(Request $request)
    {
        // 1. Nous voulons lire les données du formulaire
        // FormFactoryInterface / Request
        $form = $this->createForm(CartConfirmationType::class);

        $form->handleRequest($request);


        // 2. Si le formulaire n'a pas été soumis : sortir de la route
        // message flash puis redirection (FlahsBagInterface et Routerinterface)
        // RouterInterface qui comprend urlMatcher, pour ne pas avoir à écrire l'url en dure, mais plutôt le nom d'un route

        if (!$form->isSubmitted()) {

            $this->addFlash('warning', "Vous devez remplir le formulaire de confirmation");

            return new $this->redirectToRoute("cart_show");
        }

        // 3. Si je ne suis pas connecté : sortir de la route
        $user = $this->getUser();


        // 4. S'il n'y a pas de produits dans le panier : Sortir de la route
        // SessionInterface ou CartService

        $cart = $this->cartService->getDetailedCartItems();

        if (empty($cart)) {

            $this->addFlash('warning', 'Vous ne pouvez pas confirmer une commande avec un panier vide');

            return $this->redirectToRoute("cart_show");
        }

        // dd($data);

        // 5. Nous allons créer une Purchase
        /** @var Purchase */
        $purchase = $form->getData();

        // 6. Nous allons la lier avec l'utilisateur actuellement connecté (Security)
        $purchase->setUser($user)
            ->setPurchasedAt(new DateTime())
            ->setTotal($this->cartService->getTotal());

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

            $this->em->persist($purchaseItem);
        }

        // 8. Nous allons enregistrer la commande (EntityManagerInterface)
        $this->em->flush();

        $this->cartService->empty();

        $this->addFlash('success', "La commande à bien été prise en compte");

        return $this->redirectToRoute("purchase_index");
    }
}
