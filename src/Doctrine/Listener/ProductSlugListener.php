<?php

namespace App\Doctrine\Listener;

use App\Entity\Product;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductSlugListener
{
    protected $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    //Methode 1. 'Doctrine Lifecycle Listener'
    // public function prePersist1(LifecycleEventArgs $event)
    // {
    //     dd("ça marche");

    //     $entity = $event->getObject();

    //     if (!$entity instanceof Product) {
    //         return;
    //     }

    //     if (empty($entity->getSlug())) {
    //         $entity->setSlug(strtolower($this->slugger->slug($entity->getName())));
    //     }
    // }

    // 2. Methode 'Doctrine Entity Listener'
    public function prePersist(Product $entity)
    {

        if (empty($entity->getSlug())) {
            $entity->setSlug(strtolower($this->slugger->slug($entity->getName())));
        }
    }
}
