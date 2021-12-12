<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Form\CartConfirmationType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    /** 
     * @var CartService 
     */
    protected $cartService;

    /** 
     * @var ProductRepository 
     */
    protected $productRepository;

    public function __construct(ProductRepository $productRepository, CartService $cartService)
    {
        $this->productRepository = $productRepository;
        $this->cartService = $cartService;
    }


    /**
     * @Route("/cart/add/{id}", name="cart_add", requirements={"id":"\d+"})
     */
    public function add($id, Request $request)
    {
        // 0. Sécurisation : est-ce que le produit existe ?
        $product = $this->productRepository->find($id);

        $this->cartService->add($id);
        // $request->getSession()->remove('cart');

        $this->addFlash('success', "Le produit a bien été ajouté au panier");

        //-----
        // /** @var FlashBag */
        // $flashbag = $session->getBag('flashes');
        // $flashbag->add('success', "Le produit a bien été ajouté au panier !");
        // $flashbag->add('info', "20% offert avec le code : 2M2M !");
        // $flashbag->add('warning', "3 produits en stock seulement");
        //-----

        // dd($flashbag);

        if ($request->query->get('returnToCart')) {

            return $this->redirectToRoute('cart_show');
        }

        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'product_slug' => $product->getSlug()
        ]);
    }

    /**
     * @Route("/cart", name="cart_show")
     */
    public function show()
    {
        $form = $this->createForm(CartConfirmationType::class);

        $detailedCart = $this->cartService->getDetailedCartItems();
        $total = $this->cartService->getTotal();

        return $this->render('cart/index.html.twig', [
            'items' => $detailedCart,
            'total' => $total,
            'confirmationForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/cart/delete/{id}", name="cart_delete", requirements={"id":"\d+"})
     */
    public function delete($id)
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException("le produit $id n'existe pas. Il ne peut pas être supprimé");
        }

        $this->cartService->remove($id);

        $this->addFlash("success", "Le produit a bien été retiré du panier");

        return $this->redirectToRoute("cart_show", [], 302);
    }

    /**
     * @Route("/cart/decrement/{id}", name="cart_decrement", requirements={"id":"\d+"})
     */
    public function decrement($id)
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException("le produit $id n'existe pas. Il ne peut pas être décrémenté");
        }

        $this->cartService->decrement($id);

        $this->addFlash('success', 'le produit à bien été décrémenté');

        return $this->redirectToRoute("cart_show", [], 302);
    }
}
