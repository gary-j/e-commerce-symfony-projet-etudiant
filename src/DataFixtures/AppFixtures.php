<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Faker\Factory;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Entity\User;
use Liior\Faker\Prices;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $slugger;
    protected $encoder;

    public function __construct(SluggerInterface $slugger, UserPasswordEncoderInterface $encoder)
    {
        $this->slugger = $slugger;
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product); 

        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Prices($faker));
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

        $admin = new User;
        $hash = $this->encoder->encodePassword($admin, "password");

        $admin->setEmail("admin@gmail.com")
            ->setPassword($hash)
            ->setFullname("Admin")
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        $users = [];

        for ($u = 0; $u < 5; $u++) {
            $user = new User;
            $hash = $this->encoder->encodePassword($user, "password");
            $user->setEmail("user$u@gmail.com")
                ->setFullname($faker->name())
                ->setPassword($hash);
            $users[] = $user;
            $manager->persist($user);
        }

        for ($c = 0; $c < 3; $c++) {
            $category = new Category;
            $category
                ->setName($faker->department())
                ->setSlug(\strtolower($this->slugger->slug($category->getName())));
            $manager->persist($category);

            // Je vais stocker les produits créés dans un tableau
            $products = [];

            for ($p = 0; $p < mt_rand(15, 20); $p++) {
                $product = new Product;
                $product
                    ->setName($faker->productName())
                    ->setPrice($faker->price(4000, 20000))
                    ->setSlug(\strtolower($this->slugger->slug($product->getName())))
                    ->setCategory($category)
                    ->setShortDescription($faker->paragraph())
                    ->setMainPicture($faker->imageUrl(400, 400, true));

                $products[] = $product; // J'ajouter le produit créé dans le tableau
                $manager->persist($product);
            }
        }
        // for ($p = 0; $p < 100; $p++) {
        //     $product = new Product;
        //     $product_name = $faker->productName();

        //     $product
        //         ->setName($product_name)
        //         ->setPrice($faker->price(4000, 20000))
        //         ->setSlug(\strtolower($this->slugger->slug($product->getName())));
        //     // ->setSlug(str_replace(" ", "-", \strtolower($product_name)));
        //     $manager->persist($product);
        // }

        for ($p = 0; $p < mt_rand(20, 40); $p++) {

            $purchase = new Purchase;

            $purchase->setFullName($faker->name)
                ->setAddress($faker->streetAddress)
                ->setPostalCode($faker->postcode)
                ->setCity($faker->city)
                ->setCountry($faker->country)
                // ->setTotal(mt_rand(2000, 400000))
                ->setPurchasedAt($faker->dateTimeBetween('-6 months', 'now'))
                ->setUser($faker->randomElement($users));

            $selectedProducts = $faker->randomElements($products, mt_rand(3, 5));
            $purchaseTotal = 0;

            foreach ($selectedProducts as $product) {

                $purchaseItem = new PurchaseItem;

                $purchaseItem->setProduct($product)
                    ->setQuantity(mt_rand(1, 3))
                    ->setProductName($product->getName())
                    ->setProductPrice($product->getPrice())
                    ->setTotal($purchaseItem->getProductPrice() * $purchaseItem->getQuantity())
                    ->setPurchase($purchase);

                $purchaseTotal += $purchaseItem->getTotal();

                $manager->persist($purchaseItem);
            }

            if ($faker->boolean(85)) {
                $purchase->setStatus($purchase::STATUS_PAID);
            }

            $purchase->setTotal($purchaseTotal);

            $manager->persist($purchase);
        }
        $manager->flush();
    }
}
