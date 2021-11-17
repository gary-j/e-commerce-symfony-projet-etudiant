<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="product_category")
     */
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);
        if (!$category) {
            // throw new NotFoundHttpException("La catégorie demandée n'existe pas");
            throw $this->createNotFoundException("La catégorie demandée n'existe pas");
        }
        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }

    /**
     * @Route("/{category_slug}/{product_slug}", name="product_show")
     */
    public function show($category_slug, $product_slug, ProductRepository $productRepository, CategoryRepository $categoryRepository)
    {
        // $url = $urlGenerator->generate('product_show', [
        //     'category_slug' => $category_slug,
        //     'product_slug' => $product_slug
        // ]);

        $category = $categoryRepository->findOneBy(['slug' => $category_slug]);
        $product = $productRepository->findOneBy([
            'category' => $category,
            'slug' => $product_slug
        ]);
        if (!$product) {
            throw $this->createNotFoundException("Le produit demandé n'éxiste pas");
        }
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/admin/product/{id}/edit", name="product_edit")
     */
    public function edit($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $em, SluggerInterface $slugger, UrlGeneratorInterface $urlGenerator)
    {
        $product = $productRepository->find($id);

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        // $product = $form->getData();

        $product->setSlug(strtolower($slugger->slug($product->getName())));

        if ($form->isSubmitted()) {
            $em->flush($product);
            // dd($product);

            // $response = new Response();

            // $url = $urlGenerator->generate('product_show', [
            //     'category_slug' => $product->getCategory()->getSlug(),
            //     'product_slug' => $product->getSlug()
            // ]);

            // $response->headers->set('Location', $url);
            // $response->setStatusCode(302);

            return $this->redirectToRoute(
                'product_show',
                [
                    'category_slug' => $product->getCategory()->getSlug(),
                    'product_slug' => $product->getSlug(),
                ],
                302
            );
        }

        $formView = $form->createView();

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formView' => $formView
        ]);
    }

    /**
     * @Route("/admin/product/create", name="product_create")
     */
    public function create(CategoryRepository $categoryRepository, Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        // dump($request);
        $product = new Product;

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        // dd($request);
        if ($form->isSubmitted()) {
            // $product = $form->getData();
            $product->setSlug(strtolower($slugger->slug($product->getName())));
            $em->persist($product);
            $em->flush($product);

            dd($product);
        }
        $formView = $form->createView();


        // dd($formView);
        // dd($form);
        // dd(get_class_methods($form));

        return $this->render('product/create.html.twig', [
            'formView' => $formView
        ]);
    }
}
