<?php

namespace App\EventDispatcher;

use App\Event\ProductViewEvent;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class ProductViewEmailSubscriber implements EventSubscriberInterface
{
    protected $logger;
    protected $mailer;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
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
        // $email = new Email();

        // $email = new TemplatedEmail();

        // $email->from(new Address("contact@mail.com", "l'administrateur"))
        //     ->to("admin@mail.com")
        //     ->text("Un visiteur est sur la page du produit n° " . $productViewEvent->getProduct()->getId())
        //     ->htmlTemplate('emails/product_view.html.twig')
        //     ->context([
        //         "product" => $productViewEvent->getProduct()
        //     ])
        //     ->subject("visite produit n° " . $productViewEvent->getProduct()->getId());

        // $this->mailer->send($email);

        $this->logger->info("Le produit " . $productViewEvent->getProduct()->getName() . " a été consulté.");
    }
}
