<?php

namespace App\EventDispatcher;

use App\Event\ProductViewEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductViewEmailSubscriber implements EventSubscriberInterface
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            'product.view' => 'sendProductViewEmail'
        ];
    }

    // j'injecte l'évènement qui contien le produit, à ma méthode
    public function sendProductViewEmail(ProductViewEvent $productViewEvent)
    {
        $this->logger->info("Le produit " . $productViewEvent->getProduct()->getName() . " a été consulté.");
    }
}
